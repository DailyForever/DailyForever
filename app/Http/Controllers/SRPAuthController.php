<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Services\SRPService;
use App\Services\BackupCodeService;

class SRPAuthController extends Controller
{
    /**
     * Initiate SRP authentication
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'username' => 'required|string'
        ], [
            'username.required' => 'Username is required.'
        ]);

        try {
            $user = User::where('username', $request->username)->first();

            $challenge = null;
            if ($user && $user->hasSRPEnabled()) {
                // Real user path
                $challenge = SRPService::initiateAuthentication($user);
            } else {
                // Decoy path to mitigate username enumeration
                $config = SRPService::getConfigInfo();
                $N_hex = $config['N_hex'] ?? 'AC6BDB41324A9A9BF166DE5E1389582FAF72B6651987EE07FC3192943DB56050A37329CBB4A099ED8193E0757767A13DD52312AB4B03310DCD7F48A9DA04FD50E8083969EDB767B0CF6095179A163AB3661A05FBD5FAAAE82918A9962F0B93B855F97993EC975EEAA80D740ADBF4FF747359D041D5C33EA71D281E446B14773BCA97B43A23FB801676BD207A436C6481F1D2B9078717461A5B9D32E688F87748544523B524B0D57D5EA77A2775D2ECFA032CFBDBF52FB3786160279004E57AE6AF874E7303CE53299CCC041C7BC308D82A5698F3A8D0C38271AE35F8E9DBFBB694B5C803D89F7AE435DE236D525F54759B65E372FCD68EF20FA7111F9E4AFF73';
                $g_hex = $config['g_hex'] ?? '02';
                $bits = $config['prime_size_bits'] ?? 2048;

                $challengeId = bin2hex(random_bytes(16));
                Cache::put('srp_chal:' . $challengeId, [ 'dummy' => true ], now()->addMinutes(5));
                Cookie::queue(cookie(
                    'srp_chal',
                    $challengeId,
                    5,
                    null,
                    null,
                    app()->environment('production'),
                    true,
                    false,
                    'Lax'
                ));

                $salt = strtoupper(bin2hex(random_bytes(16)));
                $B = strtolower(bin2hex(random_bytes((int) (strlen($N_hex) / 2))));
                $challenge = [
                    'salt' => $salt,
                    'B' => $B,
                    'expires_at' => now()->addMinutes(5)->toISOString(),
                    'prime_size_bits' => $bits,
                    'N_hex' => strtoupper($N_hex),
                    'g_hex' => strtoupper($g_hex),
                ];
            }

            return response()
                ->json([
                    'success' => true,
                    'salt' => $challenge['salt'],
                    'B' => $challenge['B'],
                    'expires_at' => $challenge['expires_at'],
                    'prime_size_bits' => $challenge['prime_size_bits'] ?? null,
                    'N_hex' => $challenge['N_hex'] ?? null,
                    'g_hex' => $challenge['g_hex'] ?? null,
                    'challenge_id' => $challenge['challenge_id'] ?? null,
                ])
                ->header('Cache-Control', 'no-store');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication initiation failed'
            ], 500);
        }
    }

    /**
     * Verify SRP authentication proof
     */
    public function verify(Request $request)
    {
        $request->validate([
            'A' => [
                'required',
                'string',
                'regex:/^[0-9a-fA-F]+$/',
                'min:2',
                'max:1024', // accommodates up to 4096-bit if extended later
            ],
            'M1' => [
                'required',
                'string',
                'regex:/^[0-9a-fA-F]{64}$/', // SHA-256 hex digest
            ],
            'challenge_id' => [
                'nullable',
                'string',
                'regex:/^[0-9a-f]{32}$/', // 16-byte hex id
            ],
        ], [
            'A.required' => 'Client public key A is required.',
            'A.regex' => 'Client public key A must be hex.',
            'M1.required' => 'Client proof M1 is required.',
            'M1.regex' => 'Client proof M1 must be a 64-character hex digest.',
            'challenge_id.regex' => 'Invalid challenge identifier format.',
        ]);

        try {
            $proofData = [
                'A' => $request->A,
                'M1' => $request->M1,
                'challenge_id' => $request->input('challenge_id')
            ];

            // SRPService now returns ['user_id' => int, 'M2' => string] on success
            $auth = SRPService::verifyAuthentication($proofData);

            if (!$auth || !isset($auth['user_id'], $auth['M2'])) {
                return response()->json([
                    'error' => 'Invalid authentication proof or expired challenge'
                ], 401);
            }

            $userId = (int) $auth['user_id'];
            $serverM2 = (string) $auth['M2'];
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 401);
            }

            // Check if user needs 2FA
            if ($user->two_factor_enabled) {
                // Defer full login until TOTP is verified
                session(['2fa:user_id' => $user->id]);
                return response()
                    ->json([
                    'success' => true,
                    'requires_2fa' => true,
                    'redirect' => route('auth.2fa.show'),
                    // Provide server proof for client mutual-auth verification
                    'M2' => $serverM2,
                ])
                    ->header('Cache-Control', 'no-store');
            }

            // Log the user in
            Auth::login($user);
            $request->session()->regenerate();

            

            return response()
                ->json([
                'success' => true,
                'redirect' => route('user.dashboard'),
                // Provide server proof for client mutual-auth verification
                'M2' => $serverM2,
            ])
                ->header('Cache-Control', 'no-store');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication verification failed'
            ], 500);
        }
    }

    /**
     * Register with SRP
     */
    public function register(Request $request)
    {
        // Two modes:
        // 1) Zero-knowledge: client provides salt+verifier (preferred)
        // 2) Legacy: client provides password; server derives salt+verifier
        $isZK = $request->has('salt') && $request->has('verifier');

        if ($isZK) {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:3|max:64|unique:users,username|regex:/^[a-zA-Z0-9_-]+$/',
                'pin' => 'required|digits_between:4,8',
                'salt' => 'required|string|regex:/^[0-9a-fA-F]+$/|min:16|max:255',
                'verifier' => 'required|string|regex:/^[0-9a-fA-F]+$/|min:16|max:4096',
            ]);
        } else {
            // If Argon2id prehash is enabled on the server, disable legacy registration path to avoid mismatched verifiers
            if (env('SRP_PREHASH_ARGON2ID')) {
                return response()->json([
                    'error' => 'Legacy registration is disabled when Argon2 prehash is enabled. Please use zero-knowledge registration.'
                ], 400);
            }
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:3|max:64|unique:users,username|regex:/^[a-zA-Z0-9_-]+$/',
                'password' => 'required|string|min:8',
                'pin' => 'required|digits_between:4,8',
            ]);
        }

        if ($validator->fails()) {
            $all = $validator->errors()->all();
            $detail = is_array($all) && count($all) ? implode(' ', $all) : '';
            return response()
                ->json([
                    'error' => trim('Validation failed' . ($detail ? (': ' . $detail) : '')),
                    'messages' => $validator->errors(),
                ], 422)
                ->header('Cache-Control', 'no-store');
        }

        try {
            // Generate and encrypt backup code
            $backupCodeData = BackupCodeService::generateAndEncrypt();
            $backupCode = $backupCodeData['code'];
            $encryptedBackupCode = $backupCodeData['encrypted'];

            // Create user (use random hashed placeholder password to avoid NOT NULL errors)
            $create = [
                'name' => $request->username,
                'username' => $request->username,
                'password' => Hash::make(Str::random(40)),
            ];
            if (Schema::hasColumn('users', 'pin_hash')) {
                $create['pin_hash'] = Hash::make($request->pin);
            }
            if (Schema::hasColumn('users', 'backup_code_hash')) {
                $create['backup_code_hash'] = $encryptedBackupCode;
            }
            $user = User::create($create);

            if ($isZK) {
                // Save provided salt+verifier directly (zero-knowledge)
                $user->srp_salt = strtoupper($request->salt); // normalize to uppercase to match server hashing
                $user->srp_verifier = strtolower($request->verifier); // store hex lowercase
                // Bind the group used at enrollment to the current server group
                $cfg = \App\Services\SRPService::getConfigInfo();
                $user->srp_group_bits = $cfg['prime_size_bits'] ?? 1024;
                $user->srp_enabled = true;
                $user->srp_enabled_at = now();
                $user->save();
            } else {
                // Legacy path: derive SRP on server from password
                $srpEnabled = $user->enableSRP($request->username, $request->password);
                if (!$srpEnabled) {
                    $user->delete(); // Clean up if SRP setup fails
                    return response()->json([
                        'error' => 'Failed to set up SRP authentication. Please try again.',
                    ], 400);
                }
            }

            // Log the user in
            Auth::login($user);

            

            // Flash backup code for next full-page load (modal display)
            session()->flash('backup_code', $backupCode);

            // Respond with JSON for SPA/JS client
            return response()
                ->json([
                    'success' => true,
                    'redirect' => route('user.dashboard'),
                    'backup_code' => $backupCode,
                ])
                ->header('Cache-Control', 'no-store');

        } catch (\Exception $e) {
            $payload = [
                'error' => 'Registration failed. Please try again.',
                'error_detail' => $e->getMessage(),
            ];

            return response()
                ->json($payload, 500)
                ->header('Cache-Control', 'no-store');
        }
    }

    /**
     * Check if SRP is supported
     */
    public function checkSupport()
    {
        return response()->json([
            'supported' => SRPService::isSupported(),
            'config' => SRPService::getConfigInfo()
        ]);
    }
}