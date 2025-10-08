<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use ArtisanSdk\SRP\Server;
use ArtisanSdk\SRP\Client;
use ArtisanSdk\SRP\Config;
use phpseclib3\Math\BigInteger;
use Exception;

class SRPService
{
    /**
     * Initialize SRP authentication for a user
     * 
     * @param User $user
     * @return array
     */
    public static function initiateAuthentication(User $user): array
    {
        try {
            if (!$user->srp_enabled || !$user->srp_salt || !$user->srp_verifier) {
                throw new Exception('User does not have SRP enabled or missing SRP credentials');
            }

            // Determine group size: prefer per-user if available, else env default
            $bits = (int) ($user->srp_group_bits ?? 0);
            if (!$bits || !in_array($bits, [1024, 2048], true)) {
                $bits = self::getPrimeBits();
            }

            // Build SRP params and compute challenge primitives without caching Server object
            $N_hex = self::getNHexByBits($bits);
            $N = new BigInteger($N_hex, 16);
            $g = new BigInteger('2', 10);
            $v = new BigInteger($user->srp_verifier, 16);
            // k = H(N || PAD(g)) over binary
            $N_bin = hex2bin(str_pad($N_hex, (strlen($N_hex) + 1) & ~1, '0', STR_PAD_LEFT));
            $g_hex = '02';
            $g_bin = hex2bin(str_pad($g_hex, strlen($N_hex), '0', STR_PAD_LEFT));
            $k_hex = hash('sha256', $N_bin . $g_bin);
            $k = new BigInteger($k_hex, 16);
            // Generate private ephemeral b and compute B = (k*v + g^b) mod N
            $byteLen = (int) (strlen($N_hex) / 2);
            $bRand = bin2hex(random_bytes(max(32, $byteLen)));
            $b = new BigInteger($bRand, 16);
            // reduce to range [1, N-1]
            $Nm1 = $N->subtract(new BigInteger('1', 10));
            [, $b] = $b->divide($Nm1);
            if ($b->compare(new BigInteger('0', 10)) === 0) {
                $b = new BigInteger('1', 10);
            }
            $gb = $g->powMod($b, $N);
            // kv mod N
            $kv = $k->multiply($v);
            [, $kvModN] = $kv->divide($N);
            // (kvModN + gb) mod N
            $sum = $kvModN->add($gb);
            [, $Bint] = $sum->divide($N);
            $B = strtolower($Bint->toHex());

            // Create a short-lived challenge ID and store primitives in cache
            $challengeId = bin2hex(random_bytes(16));
            Cache::put('srp_chal:'.$challengeId, [
                'b' => strtolower($b->toHex()),
                'B' => $B,
                'v' => strtolower($user->srp_verifier),
                's' => strtoupper($user->srp_salt),
                'N_hex' => strtoupper($N_hex),
                'g_hex' => $g_hex,
                'k_hex' => strtolower($k_hex),
                'user_id' => $user->id,
                'bits' => $bits,
            ], now()->addMinutes(5));

            // Queue HttpOnly cookie carrying the challenge ID (5 minutes)
            Cookie::queue(cookie(
                'srp_chal',
                $challengeId,
                5, // minutes
                null,
                null,
                app()->environment('production'), // secure in production
                true, // httpOnly
                false,
                'Lax'
            ));
            
            return [
                'salt' => $user->srp_salt,
                'B' => $B,
                'expires_at' => now()->addMinutes(5)->toISOString(),
                'prime_size_bits' => $bits,
                'N_hex' => strtoupper($N_hex),
                'g_hex' => '02',
                'challenge_id' => $challengeId,
            ];
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Verify SRP authentication proof
     * 
     * @param array $proofData
     * @return array|null ['user_id' => int, 'M2' => string] on success, null on failure
     */
    public static function verifyAuthentication(array $proofData): ?array
    {
        try {
            // Read challenge ID from payload (preferred when cookies are blocked/cleared) or from cookie
            $challengeId = $proofData['challenge_id'] ?? Cookie::get('srp_chal');
            if (!$challengeId) {
                throw new Exception('SRP challenge missing');
            }

            $cached = Cache::pull('srp_chal:'.$challengeId);
            if (!$cached || !isset($cached['b'], $cached['B'], $cached['v'], $cached['N_hex'], $cached['g_hex'], $cached['k_hex'], $cached['user_id'])) {
                throw new Exception('SRP challenge expired');
            }

            $userId = $cached['user_id'];
            
            $A = $proofData['A'] ?? null;
            $M1 = $proofData['M1'] ?? null;
            
            if (!$A || !$M1) {
                throw new Exception('Missing SRP proof data');
            }
            
            // Normalize A and M1 to lowercase hex
            if (is_string($A)) $A = strtolower(trim($A));
            if (is_string($M1)) $M1 = strtolower(trim($M1));

            // Reconstruct parameters
            $N = new BigInteger($cached['N_hex'], 16);
            $g = new BigInteger($cached['g_hex'], 16);
            $k = new BigInteger($cached['k_hex'], 16);
            $v = new BigInteger($cached['v'], 16);
            $Bint = new BigInteger($cached['B'], 16);
            $b = new BigInteger($cached['b'], 16);
            $Aint = new BigInteger($A, 16);

            // Validate A and B not zero mod N
            [, $Arem] = $Aint->divide($N);
            if ($Arem->compare(new BigInteger('0', 10)) === 0) {
                throw new Exception('Invalid SRP A');
            }
            [, $Brem] = $Bint->divide($N);
            if ($Brem->compare(new BigInteger('0', 10)) === 0) {
                throw new Exception('Invalid SRP B');
            }

            // Compute u = H(A||B) over ASCII hex
            $u_hex = hash('sha256', strtolower($A) . strtolower($cached['B']));
            $u = new BigInteger($u_hex, 16);

            // Compute S = (A * v^u)^b mod N
            $vu = $v->powMod($u, $N);
            $prod = $Aint->multiply($vu);
            [, $base] = $prod->divide($N);
            $S = $base->powMod($b, $N);
            $S_hex = strtolower($S->toHex());

            // Expected M1 = H(A||B||S)
            $M1_expected = hash('sha256', strtolower($A) . strtolower($cached['B']) . $S_hex);
            if (!hash_equals($M1_expected, $M1)) {
                throw new Exception('SRP proof mismatch');
            }

            // Compute server proof M2 = H(A || M1 || S) over ASCII hex, lowercase
            $M2 = hash('sha256', strtolower($A) . $M1 . $S_hex);
            // Clear challenge cookie if present
            if (Cookie::get('srp_chal')) {
                Cookie::queue(Cookie::forget('srp_chal'));
            }

            // Update user's last SRP login time
            $user = User::find($userId);
            if ($user) {
                $user->srp_last_login_at = now();
                $user->save();
            }

            return [
                'user_id' => (int) $userId,
                'M2' => $M2,
            ];
            
        } catch (Exception $e) {
            // Ensure challenge is cleared on error
            if ($id = Cookie::get('srp_chal')) {
                Cache::forget('srp_chal:'.$id);
                Cookie::queue(Cookie::forget('srp_chal'));
            }

            return null;
        }
    }
    
    /**
     * Generate SRP credentials for a user
     * 
     * @param string $username
     * @param string $password
     * @return array
     */
    public static function generateCredentials(string $username, string $password): array
    {
        try {
            $config = self::getSRPConfig();
            $client = new Client($config);
            
            // Generate random salt (hex) and verifier using SRP client
            $salt = bin2hex(random_bytes(16)); // 128-bit salt in hex
            $verifier = $client->enroll($username, $password, $salt);
            
            return [
                'salt' => $salt,
                'verifier' => $verifier
            ];
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Enable SRP for a user
     * 
     * @param User $user
     * @param string $username
     * @param string $password
     * @return bool
     */
    public static function enableSRP(User $user, string $username, string $password): bool
    {
        try {
            $credentials = self::generateCredentials($username, $password);
            
            $user->srp_salt = $credentials['salt'];
            $user->srp_verifier = $credentials['verifier'];
            $user->srp_group_bits = self::getPrimeBits();
            $user->srp_enabled = true;
            $user->srp_enabled_at = now();
            $user->save();
            
            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Disable SRP for a user
     * 
     * @param User $user
     * @return bool
     */
    public static function disableSRP(User $user): bool
    {
        try {
            $user->srp_salt = null;
            $user->srp_verifier = null;
            $user->srp_enabled = false;
            $user->srp_enabled_at = null;
            $user->srp_last_login_at = null;
            $user->save();
            
            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Check if SRP is supported
     * 
     * @return bool
     */
    public static function isSupported(): bool
    {
        try {
            // Check if required extensions are available
            if (!extension_loaded('gmp') && !extension_loaded('bcmath')) {
                return false;
            }
            
            // Try to create SRP instances with RFC5054 2048-bit configuration
            $config = self::getSRPConfig();
            new Server($config);
            new Client($config);
            
            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get SRP configuration info
     * 
     * @return array
     */
    public static function getConfigInfo(): array
    {
        $bits = self::getPrimeBits();
        $N_hex = strtoupper(self::getNHexByBits($bits));
        return [
            'algorithm' => 'SRP-6a',
            'hash_function' => 'SHA-256',
            'prime_group' => sprintf('RFC5054 %d-bit group', $bits),
            'prime_size_bits' => $bits,
            'available_prime_sizes' => [1024, 2048],
            'N_hex' => $N_hex,
            'g_hex' => '02',
            'generator' => 2,
            'k_hash' => 'SHA-256 (k = H(N || PAD(g)))',
            'verifier_kdf' => 'x = H( UPPER(s || H(I":"P)) ) via SHA-256 per library',
            'prehash' => [
                'enabled' => (bool) env('SRP_PREHASH_ARGON2ID', false),
                'recommendation' => 'Argon2id prehash of password client-side before SRP (e.g., m=64-128MB, t=3, p=1) requires re-enrollment'
            ],
            'transport' => [
                'tls_required' => app()->environment('production'),
                'hsts_recommended' => true,
            ],
            'implementation' => [
                'server_library' => 'artisansdk/srp',
                'client_library' => 'secure-remote-password',
            ],
            'supported' => self::isSupported(),
            'description' => 'Secure Remote Password protocol - true zero-knowledge authentication'
        ];
    }

    /**
     * Build SRP configuration matching selected RFC 5054 group and SHA-256.
     * Uses phpseclib BigInteger to convert hex constants to decimal strings.
     *
     * @param int|null $bits Optional override for group size (e.g., 1024 or 2048)
     */
    protected static function getSRPConfig(?int $bits = null): Config
    {
        $bits = $bits && in_array($bits, [1024, 2048], true) ? $bits : self::getPrimeBits();
        $N_hex = self::getNHexByBits($bits);
        $g_dec = '2';

        // Convert N from hex to decimal string
        $N_dec = (new BigInteger($N_hex, 16))->toString();

        // Compute k = H(N | PAD(g)) with SHA-256; pad g to same byte length as N
        $N_bin = hex2bin(str_pad($N_hex, (strlen($N_hex) + 1) & ~1, '0', STR_PAD_LEFT));
        $g_hex = '02';
        $g_bin = hex2bin(str_pad($g_hex, strlen($N_hex), '0', STR_PAD_LEFT));
        $k_hex = hash('sha256', $N_bin . $g_bin);

        return new Config($N_dec, $g_dec, $k_hex, 'sha256');
    }

    /**
     * Get active SRP prime size from env, default 2048, validated to supported set.
     */
    protected static function getPrimeBits(): int
    {
        $bits = (int) env('SRP_PRIME_BITS', 2048);
        return in_array($bits, [1024, 2048], true) ? $bits : 2048;
    }

    /**
     * Return RFC 5054 N in hex for supported bit lengths.
     */
    protected static function getNHexByBits(int $bits): string
    {
        switch ($bits) {
            case 1024:
                return str_replace([' ', "\n", "\r\n"], '',
                    'EEAF0AB9 ADB38DD6 9C33F80A FA8FC5E8 60726187 75FF3C0B 9EA2314C'
                  . '9C256576 D674DF74 96EA81D3 383B4813 D692C6E0 E0D5D8E2 50B98BE4'
                  . '8E495C1D 6089DAD1 5DC7D7B4 6154D6B6 CE8EF4AD 69B15D49 82559B29'
                  . '7BCF1885 C529F566 660E57EC 68EDBC3C 05726CC0 2FD4CBF4 976EAA9A'
                  . 'FD5138FE 8376435B 9FC61D2F C0EB06E3'
                );
            case 2048:
            default:
                return str_replace([' ', "\n", "\r\n"], '',
                    'AC6BDB41 324A9A9B F166DE5E 1389582F AF72B665 1987EE07 FC319294'
                  . '3DB56050 A37329CB B4A099ED 8193E075 7767A13D D52312AB 4B03310D'
                  . 'CD7F48A9 DA04FD50 E8083969 EDB767B0 CF609517 9A163AB3 661A05FB'
                  . 'D5FAAAE8 2918A996 2F0B93B8 55F97993 EC975EEA A80D740A DBF4FF74'
                  . '7359D041 D5C33EA7 1D281E44 6B14773B CA97B43A 23FB8016 76BD207A'
                  . '436C6481 F1D2B907 8717461A 5B9D32E6 88F87748 544523B5 24B0D57D'
                  . '5EA77A27 75D2ECFA 032CFBDB F52FB378 61602790 04E57AE6 AF874E73'
                  . '03CE5329 9CCC041C 7BC308D8 2A5698F3 A8D0C382 71AE35F8 E9DBFBB6'
                  . '94B5C803 D89F7AE4 35DE236D 525F5475 9B65E372 FCD68EF2 0FA7111F'
                  . '9E4AFF73'
                );
        }
    }
}
