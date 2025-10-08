<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use phpseclib3\Math\BigInteger;

class SRPAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure migrations are run
        $this->artisan('migrate');
    }

    private static function sha256Ascii(string $ascii): string
    {
        return strtolower(hash('sha256', $ascii));
    }

    private static function sha256HexBytes(string $hex): string
    {
        // Normalize to even-length hex
        $hex = (strlen($hex) % 2 === 0) ? $hex : ("0" . $hex);
        $bin = hex2bin($hex);
        return strtolower(hash('sha256', $bin));
    }

    private static function unpadHex(string $hex): string
    {
        $hex = ltrim($hex, '0');
        return $hex === '' ? '0' : $hex;
    }

    private function clientDeriveAandM1(array $challenge, string $username, string $password): array
    {
        // Params from server
        $N_hex = strtoupper($challenge['N_hex']);
        $g_hex = strtoupper($challenge['g_hex'] ?? '02');
        $saltUpper = strtoupper($challenge['salt']);
        $Bhex = strtolower($challenge['B']);

        $N = new BigInteger($N_hex, 16);
        $g = new BigInteger($g_hex, 16);

        // k = H(N || PAD(g)) over bytes
        $gPadded = str_pad($g_hex, strlen($N_hex), '0', STR_PAD_LEFT);
        $k_hex = self::sha256HexBytes($N_hex . $gPadded);
        $k = new BigInteger($k_hex, 16);

        // Client private ephemeral a in [1, N-1]
        $byteLen = (int) (strlen($N_hex) / 2);
        $aRand = bin2hex(random_bytes(max(32, $byteLen)));
        $a = new BigInteger($aRand, 16);
        $Nm1 = $N->subtract(new BigInteger('1', 10));
        [, $a] = $a->divide($Nm1);
        if ($a->compare(new BigInteger('0', 10)) === 0) {
            $a = new BigInteger('1', 10);
        }

        $Aint = $g->powMod($a, $N);
        $Ahex = strtolower(self::unpadHex($Aint->toHex()));

        // Derive x matching client logic: x = H( UPPER(s + unpad(H(I+":"+P)).toUpperCase()) )
        $inner = self::sha256Ascii($username . ':' . $password);
        $innerUnpadded = strtoupper(self::unpadHex($inner));
        $xHex = self::sha256Ascii($saltUpper . $innerUnpadded);
        $x = new BigInteger($xHex, 16);

        // Normalize server B
        $Bclean = ltrim(strtolower($Bhex));
        $Bint = new BigInteger($Bclean, 16);

        // u = H(A||B) over lowercase ascii hex
        $u_hex = self::sha256Ascii($Ahex . $Bclean);
        $u = new BigInteger($u_hex, 16);

        // S = (B - k * g^x) ^ (a + u * x) mod N
        $gx = $g->powMod($x, $N);
        $kgx = $k->multiply($gx);
        [, $kgxModN] = $kgx->divide($N);
        $diff = $Bint->subtract($kgxModN);
        [, $base] = $diff->divide($N);
        $exp = $a->add($u->multiply($x));
        $S = $base->powMod($exp, $N);
        $S_hex = strtolower(self::unpadHex($S->toHex()));

        // M1 = H(A||B||S)
        $M1 = self::sha256Ascii($Ahex . $Bclean . $S_hex);

        return [
            'A' => $Ahex,
            'M1' => $M1,
        ];
    }

    public function test_srp_initiate_and_verify_success(): void
    {
        $username = 'alice';
        $password = 'correct horse battery staple';

        $user = User::create([
            'name' => $username,
            'username' => $username,
            'password' => bcrypt('placeholder'),
        ]);

        // Enable SRP (server derives salt+verifier from password)
        $this->assertTrue($user->enableSRP($username, $password));

        // Initiate SRP
        $resp = $this->postJson('/api/srp/initiate', ['username' => $username]);
        $resp->assertOk();
        $data = $resp->json();
        $this->assertArrayHasKey('salt', $data);
        $this->assertArrayHasKey('B', $data);
        $this->assertArrayHasKey('N_hex', $data);
        $this->assertArrayHasKey('g_hex', $data);
        $this->assertArrayHasKey('challenge_id', $data);

        // Compute client proof
        $proof = $this->clientDeriveAandM1($data, $username, $password);

        // Verify
        $verify = $this->postJson('/api/srp/verify', [
            'A' => $proof['A'],
            'M1' => $proof['M1'],
            'challenge_id' => $data['challenge_id'],
        ]);
        $verify->assertOk();
        $verify->assertJson(['success' => true]);

        // Challenge must be one-time: reuse should fail
        $verify2 = $this->postJson('/api/srp/verify', [
            'A' => $proof['A'],
            'M1' => $proof['M1'],
            'challenge_id' => $data['challenge_id'],
        ]);
        $verify2->assertStatus(401);
    }

    public function test_srp_verify_bad_m1_fails(): void
    {
        $username = 'bob';
        $password = 'hunter2';

        $user = User::create([
            'name' => $username,
            'username' => $username,
            'password' => bcrypt('placeholder'),
        ]);
        $this->assertTrue($user->enableSRP($username, $password));

        $resp = $this->postJson('/api/srp/initiate', ['username' => $username]);
        $resp->assertOk();
        $data = $resp->json();

        // Generate a valid A but corrupt M1
        $proof = $this->clientDeriveAandM1($data, $username, $password);
        $badM1 = str_repeat('0', 64);

        $verify = $this->postJson('/api/srp/verify', [
            'A' => $proof['A'],
            'M1' => $badM1,
            'challenge_id' => $data['challenge_id'],
        ]);
        $verify->assertStatus(401);
    }
}
