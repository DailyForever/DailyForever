<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paste;
use App\Models\Prekey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class PasteController extends Controller
{
    public function index()
    {
        return view('paste.create');
    }

    public function mine(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        $pastes = Paste::where('user_id', Auth::id())
            ->orderByDesc('id')
            ->limit(50)
            ->get();
        return view('paste.mine', compact('pastes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'encrypted_content' => 'required|string',
            'iv' => 'required|string',
            'expires_in' => 'nullable|in:1hour,1day,1week,1month,never',
            'view_limit' => 'nullable|integer|min:1|max:1000000',
            'recipient_username' => 'nullable|string',
            'kem_alg' => 'nullable|string|max:32',
            'kem_kid' => 'nullable|string|max:64',
            'kem_ct' => 'nullable|string', // base64
            'kem_wrapped_key' => 'nullable|string', // base64 (salt+iv+ct)
            'encryption_key' => 'nullable|string|max:512',
        ]);

        $expiresAt = null;
        if ($request->expires_in && $request->expires_in !== 'never') {
            switch ($request->expires_in) {
                case '1hour':
                    $expiresAt = Carbon::now()->addHour();
                    break;
                case '1day':
                    $expiresAt = Carbon::now()->addDay();
                    break;
                case '1week':
                    $expiresAt = Carbon::now()->addWeek();
                    break;
                case '1month':
                    $expiresAt = Carbon::now()->addMonth();
                    break;
            }
        }
        // Round timestamps with small jitter (metadata-hiding)
        $createdAt = Carbon::now()->startOfHour()->addMinutes(random_int(0, 59));

        $recipientId = null;
        if ($request->filled('recipient_username')) {
            $recipient = \App\Models\User::where('username', $request->recipient_username)->first();
            $recipientId = $recipient?->id;
        }
        // Normalize IV JSON to guard input format
        $ivNorm = null;
        try {
            $ivArr = json_decode((string) $request->iv, true, 512, JSON_THROW_ON_ERROR);
            if (!\is_array($ivArr) || \count($ivArr) !== 12) {
                return response()->json(['error' => 'Invalid IV'], 422);
            }
            foreach ($ivArr as $v) {
                if (!\is_int($v) && !ctype_digit((string)$v)) return response()->json(['error' => 'Invalid IV bytes'], 422);
                $i = (int)$v; if ($i < 0 || $i > 255) return response()->json(['error' => 'Invalid IV byte range'], 422);
            }
            $ivNorm = json_encode(array_map('intval', $ivArr));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid IV'], 422);
        }

        
        // Validate KEM metadata if present
        $kemAlg = $request->input('kem_alg');
        $kemKid = $request->input('kem_kid');
        $kemCtRaw = null; $kemWrappedRaw = null;
        if ($kemAlg || $kemKid || $request->filled('kem_ct') || $request->filled('kem_wrapped_key')) {
            if (!$kemAlg || !$kemKid || !$request->filled('kem_ct') || !$request->filled('kem_wrapped_key')) {
                return response()->json(['error' => 'Invalid KEM metadata'], 422);
            }
            if (!$recipientId) {
                return response()->json(['error' => 'Recipient required for addressed pastes'], 422);
            }
            $allowedAlgs = ['ML-KEM-512','ML-KEM-768','ML-KEM-1024'];
            if (!in_array($kemAlg, $allowedAlgs, true)) {
                return response()->json(['error' => 'Unsupported KEM algorithm'], 422);
            }
            $kemCtRaw = base64_decode($request->kem_ct, true);
            $kemWrappedRaw = base64_decode($request->kem_wrapped_key, true);
            if ($kemCtRaw === false || $kemWrappedRaw === false) {
                return response()->json(['error' => 'Invalid KEM data encoding'], 422);
            }
            if (strlen($kemCtRaw) < 16 || strlen($kemCtRaw) > 8192) {
                return response()->json(['error' => 'KEM ciphertext size invalid'], 422);
            }
            if (strlen($kemWrappedRaw) < (32 + 12 + 16) || strlen($kemWrappedRaw) > (32 + 12 + 8192)) {
                return response()->json(['error' => 'Wrapped key size invalid'], 422);
            }
        }

        $user = Auth::user();
        $shouldStoreKey = $user && $user->store_encryption_keys;
        $encryptionKey = null;
        if ($shouldStoreKey) {
            $rawKey = trim((string) $request->input('encryption_key', ''));
            if ($rawKey !== '') {
                if (!preg_match('/^[0-9a-fA-F]+$/', $rawKey) || strlen($rawKey) % 2 !== 0) {
                    return response()->json(['error' => 'Invalid encryption key'], 422);
                }
                $encryptionKey = strtolower($rawKey);
            }
        }

        $paste = Paste::create([
            'identifier' => Paste::generateIdentifier(),
            'encrypted_content' => $request->encrypted_content,
            'iv' => $ivNorm,
            'expires_at' => $expiresAt,
            'user_id' => Auth::id(), // Will be null for guests
            // Respect no-logs policy: do not persist IP addresses
            'uploader_ip' => null,
            'view_limit' => $request->input('view_limit'),
            'is_private' => Auth::check() ? (bool) $request->boolean('is_private') : false, // Guests cannot create private pastes
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
            'kem_alg' => $kemAlg,
            'kem_kid' => $kemKid,
            'kem_ct' => $kemCtRaw,
            'kem_wrapped_key' => $kemWrappedRaw,
            'recipient_id' => $recipientId,
            'password_hash' => $request->filled('password') ? password_hash($request->password, PASSWORD_ARGON2ID) : null,
            'password_hint' => $request->input('password_hint'),
            // Store owner encryption key only when the user explicitly opts-in
            'encryption_key' => $encryptionKey,
        ]);

        // Server-side authoritative mark-used for recipient's prekey when addressed
        try {
            if ($recipientId && $request->filled('kem_kid')) {
                Prekey::where('user_id', $recipientId)
                    ->where('kid', $request->input('kem_kid'))
                    ->update(['used_at' => now()]);
            }
        } catch (\Throwable $e) {
            // Non-fatal
        }

        return response()->json([
            'success' => true,
            'url' => route('paste.show', $paste->identifier),
            'identifier' => $paste->identifier,
        ]);
    }

    public function show($identifier)
    {
        $start = microtime(true);
        $paste = Paste::where('identifier', $identifier)->first();
        // Lightweight password check without leaking content
        if (request()->query('pw_check') && $paste && $paste->password_hash) {
            // Throttle pw_check to mitigate brute force
            try {
                $ip = request()->ip();
                $key = 'pw_check:paste:'.$paste->id.':'.($ip ?: 'unknown');
                if (RateLimiter::tooManyAttempts($key, 30)) {
                    return response()->json(['error' => 'Too many attempts'], 429);
                }
                RateLimiter::hit($key, 60); // decay after 60 seconds
            } catch (\Throwable $e) {
                // If RateLimiter fails, do not block; continue gracefully
            }
            $pwd = request()->header('X-Paste-Password');
            if ($pwd && password_verify($pwd, $paste->password_hash)) {
                return response()->noContent();
            }
            return response()->json(['error' => 'password_required', 'hint' => $paste->password_hint], 401);
        }
        $notAllowed = !$paste || $paste->isExpired() || $paste->is_removed || $paste->hasReachedViewLimit();
        // Simulate constant-time by doing a tiny fake delay to blur found/not-found
        $elapsed = microtime(true) - $start;
        if ($elapsed < 0.02) { usleep((int)((0.02 - $elapsed) * 1e6)); }
        if ($notAllowed) {
            return view('paste.not-found');
        }

        // Enforce privacy: only the owner may view private pastes
        if ($paste->is_private && (!Auth::check() || Auth::id() !== $paste->user_id)) {
            abort(403);
        }

        // Enforce addressed pastes: only the intended recipient may view
        if ($paste->recipient_id && (!Auth::check() || Auth::id() !== $paste->recipient_id)) {
            abort(403);
        }

        // Check if user is the owner - if so, they can view without decryption
        $isOwner = Auth::check() && Auth::id() === $paste->user_id;

        // Check if this view would reach the limit BEFORE incrementing
        $wouldReachLimit = $paste->view_limit !== null && $paste->views >= $paste->view_limit;
        
        // Load files for display
        $paste->load('files');
        
        // Display the content first
        $response = view('paste.show', compact('paste', 'isOwner'));
        
        // Now increment views after successful display
        $paste->incrementViews();
        
        // Check if this view reached the limit and delete if necessary
        if ($paste->view_limit !== null && $paste->views >= $paste->view_limit) {
            // Delete the paste after the response is sent
            $paste->delete();
        }
        
        return $response;
    }

    public function raw($identifier)
    {
        $start = microtime(true);
        $paste = Paste::where('identifier', $identifier)->first();

        // Apply the same visibility constraints as show(): expired, removed, or view-limited are hidden
        $notAllowed = !$paste || $paste->isExpired() || $paste->is_removed || $paste->hasReachedViewLimit();
        // Constant-time style: blur existence with a tiny delay window
        $elapsed = microtime(true) - $start;
        if ($elapsed < 0.02) { usleep((int)((0.02 - $elapsed) * 1e6)); }
        if ($notAllowed) {
            return response()->json(['error' => 'Paste not found or expired'], 404);
        }

        // Enforce privacy: owner-only for private; recipient-only for addressed
        if ($paste->is_private && (!Auth::check() || Auth::id() !== $paste->user_id)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($paste->recipient_id && (!Auth::check() || Auth::id() !== $paste->recipient_id)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'encrypted_content' => $paste->encrypted_content,
            'iv' => $paste->iv
        ]);
    }

    public function edit($identifier)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }

        $paste = Paste::where('identifier', $identifier)->first();
        
        if (!$paste) {
            abort(404);
        }

        // Only the owner can edit
        if (Auth::id() !== $paste->user_id) {
            abort(403);
        }

        return view('paste.edit', compact('paste'));
    }

    public function update(Request $request, $identifier)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $paste = Paste::where('identifier', $identifier)->first();
        
        if (!$paste) {
            return response()->json(['error' => 'Paste not found'], 404);
        }

        // Only the owner can update
        if (Auth::id() !== $paste->user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate([
            'encrypted_content' => 'required|string',
            'iv' => 'required|string',
            'expires_in' => 'nullable|in:1hour,1day,1week,1month,never',
            'view_limit' => 'nullable|integer|min:1|max:1000000',
            'is_private' => 'nullable|boolean',
            'password' => 'nullable|string|max:128',
            'password_hint' => 'nullable|string|max:128',
            'encryption_key' => 'nullable|string|max:512',
        ]);

        $expiresAt = null;
        if ($request->expires_in && $request->expires_in !== 'never') {
            switch ($request->expires_in) {
                case '1hour':
                    $expiresAt = Carbon::now()->addHour();
                    break;
                case '1day':
                    $expiresAt = Carbon::now()->addDay();
                    break;
                case '1week':
                    $expiresAt = Carbon::now()->addWeek();
                    break;
                case '1month':
                    $expiresAt = Carbon::now()->addMonth();
                    break;
            }
        }

        $user = Auth::user();
        $shouldStoreKey = $user && $user->store_encryption_keys;
        $encryptionKey = $paste->encryption_key;
        if ($shouldStoreKey) {
            $rawKey = trim((string) $request->input('encryption_key', ''));
            if ($rawKey !== '') {
                if (!preg_match('/^[0-9a-fA-F]+$/', $rawKey) || strlen($rawKey) % 2 !== 0) {
                    return response()->json(['error' => 'Invalid encryption key'], 422);
                }
                $encryptionKey = strtolower($rawKey);
            }
        } else {
            $encryptionKey = null;
        }

        $paste->update([
            'encrypted_content' => $request->encrypted_content,
            'iv' => $request->iv,
            'expires_at' => $expiresAt,
            'view_limit' => $request->input('view_limit'),
            'is_private' => (bool) $request->boolean('is_private'),
            'password_hash' => $request->filled('password') ? password_hash($request->password, PASSWORD_ARGON2ID) : $paste->password_hash,
            'password_hint' => $request->input('password_hint'),
            // Persist owner encryption key only when allowed by user preference
            'encryption_key' => $encryptionKey,
        ]);

        return response()->json([
            'success' => true,
            'url' => route('paste.show', $paste->identifier),
            'identifier' => $paste->identifier,
        ]);
    }

    public function destroy($identifier)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $paste = Paste::where('identifier', $identifier)->first();
        
        if (!$paste) {
            return response()->json(['error' => 'Paste not found'], 404);
        }

        // Only the owner can delete
        if (Auth::id() !== $paste->user_id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $paste->delete();

        return response()->json(['success' => true]);
    }
}
