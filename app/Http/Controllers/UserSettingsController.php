<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSettingsController extends Controller
{
    public function show()
    {
        if (!Auth::check()) return redirect()->route('auth.login.show');
        return view('user.settings');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|max:255|unique:users,email,' . Auth::id(),
        ], [
            'email.unique' => 'This email address is already in use by another account.',
        ]);

        $user = Auth::user();
        $user->email = $request->email;
        $user->save();

        return back()->with('status', $request->email ? 'Email updated successfully' : 'Email removed successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8',
        ]);
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password']);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('status', 'Password updated');
    }

    public function email(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
        ]);
        $user = Auth::user();
        $user->email = $request->email ?: null;
        $user->save();
        return back()->with('status', 'Email saved');
    }

    public function qr()
    {
        // Generate an otpauth URI and return a local QR image; store temp secret in session
        $user = Auth::user();
        if (!$user) abort(403);
        $secret = session('2fa_secret');
        if (!$secret) {
            $secret = $this->base32Encode(random_bytes(20));
            session(['2fa_secret' => $secret]);
        }
        if (!preg_match('/^[A-Z2-7]+$/', $secret)) {
            $secret = $this->base32Encode(random_bytes(20));
            session(['2fa_secret' => $secret]);
        }
        $issuerRaw = config('app.name', 'DailyForever');
        $accountRaw = $user->username;
        $label = rawurlencode($issuerRaw . ':' . $accountRaw);
        $imageUrl = null;
        if (file_exists(public_path('android-chrome-512x512.png'))) {
            $imageUrl = asset('android-chrome-512x512.png');
        } elseif (file_exists(public_path('favicon.ico'))) {
            $imageUrl = asset('favicon.ico');
        } else {
            $imageUrl = asset('logo.png');
        }
        $uri = 'otpauth://totp/' . $label
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuerRaw)
            . '&algorithm=SHA1&digits=6&period=30'
            . '&image=' . rawurlencode($imageUrl);

        // Prefer Simple QrCode (PNG). Return image bytes instead of redirecting to third party
        try {
            if (class_exists('SimpleSoftwareIO\\QrCode\\Facades\\QrCode')) {
                $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(180)
                    ->margin(2)
                    ->generate($uri);
                return response($png)->header('Content-Type', 'image/png')->header('Cache-Control', 'no-store');
            }
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        // Fallback to BaconQrCode (SVG)
        if (class_exists('BaconQrCode\\Writer')) {
            try {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180, 2),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $svg = $writer->writeString($uri);
                return response($svg)->header('Content-Type', 'image/svg+xml')->header('Cache-Control', 'no-store');
            } catch (\Throwable $e) {
                // ignore and fallback
            }
        }

        // Last resort: fallback to Google Charts to avoid breaking flow if local generators unavailable
        $qr = 'https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=' . urlencode($uri);
        return redirect($qr);
    }

    public function enable2fa(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = Auth::user();
        $secret = session('2fa_secret');
        if (!$secret) return back()->withErrors(['code' => 'Secret missing, refresh page']);
        if (!$this->verifyTotp($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code']);
        }
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = true;
        $user->save();
        session()->forget('2fa_secret');
        return back()->with('status', '2FA enabled');
    }

    public function disable2fa()
    {
        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();
        return back()->with('status', '2FA disabled');
    }

    public function otpauth()
    {
        $user = Auth::user();
        if (!$user) abort(403);
        $secret = session('2fa_secret');
        if (!$secret) {
            $secret = $this->base32Encode(random_bytes(20));
            session(['2fa_secret' => $secret]);
        }
        if (!preg_match('/^[A-Z2-7]+$/', $secret)) {
            $secret = $this->base32Encode(random_bytes(20));
            session(['2fa_secret' => $secret]);
        }
        $issuerRaw = config('app.name', 'DailyForever');
        $accountRaw = $user->username;
        $label = rawurlencode($issuerRaw . ':' . $accountRaw);

        if (file_exists(public_path('android-chrome-512x512.png')))
        {
            $imageUrl = asset('android-chrome-512x512.png');
        }
        elseif (file_exists(public_path('favicon.ico')))
        {
            $imageUrl = asset('favicon.ico');
        }
        else
        {
            $imageUrl = asset('logo.png');
        }
        // Build otpauth URI
        $uri = 'otpauth://totp/' . $label
            . '?secret=' . rawurlencode($secret)
            . '&issuer=' . rawurlencode($issuerRaw)
            . '&algorithm=SHA1&digits=6&period=30'
            . '&image=' . rawurlencode($imageUrl);

        $qrValue = null;
        try {
            if (class_exists('SimpleSoftwareIO\\QrCode\\Facades\\QrCode')) {
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(200)
                    ->margin(2)
                    ->generate($uri);
                $qrValue = 'data:image/png;base64,' . base64_encode($qrCode);
            }
        } catch (\Throwable $e) {
            // ignore and fallback below
        }
        // Fallback to BaconQrCode (SVG) if available
        if (!$qrValue && class_exists('BaconQrCode\\Writer')) {
            try {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200, 2),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $svg = $writer->writeString($uri);
                $qrValue = 'data:image/svg+xml;base64,' . base64_encode($svg);
            } catch (\Throwable $e) {
                // ignore and fallback below
            }
        }
        if (!$qrValue) {
            $qrValue = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($uri);
        }

        return response()
            ->json([
                'secret' => $secret,
                'issuer' => $issuerRaw,
                'account' => $accountRaw,
                'uri' => $uri,
                'image' => $imageUrl,
                'qr_code' => $qrValue,
            ])
            ->header('Cache-Control', 'no-store');
    }

    private function verifyTotp(string $secret, string $code): bool
    {
        // Verify TOTP with 30s period and 1-step window; Base32 secret per RFC4648 (no padding)
        $key = $this->base32Decode($secret);
        $timeSlice = floor(time() / 30);
        for ($i = -1; $i <= 1; $i++) {
            $counter = pack('N*', 0) . pack('N*', $timeSlice + $i);
            $hmac = hash_hmac('sha1', $counter, $key, true);
            $offset = ord($hmac[19]) & 0x0F;
            $hashPart = substr($hmac, $offset, 4);
            $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
            $otp = str_pad((string)($value % 1000000), 6, '0', STR_PAD_LEFT);
            if (hash_equals($otp, preg_replace('/\s+/', '', $code))) {
                return true;
            }
        }
        return false;
    }

    private function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($data) as $c) {
            $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $encoded = '';
        foreach (str_split($bits, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $encoded .= $alphabet[bindec($chunk)];
        }
        return $encoded; // no padding
    }

    private function base32Decode(string $encoded): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $map = array_flip(str_split($alphabet));
        $bits = '';
        $encoded = strtoupper(preg_replace('/[^A-Z2-7]/', '', $encoded));
        foreach (str_split($encoded) as $c) {
            if (!isset($map[$c])) continue;
            $bits .= str_pad(decbin($map[$c]), 5, '0', STR_PAD_LEFT);
        }
        $out = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $out .= chr(bindec($chunk));
            }
        }
        return $out;
    }

    public function updateEncryptionKeyPreference(Request $request)
    {
        $request->validate([
            'store_encryption_keys' => 'required|boolean',
        ]);
        
        $user = Auth::user();
        $user->store_encryption_keys = $request->store_encryption_keys;
        $user->save();
        
        return back()->with('status', 'Encryption key storage preference updated');
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if ($request->confirmation !== 'DELETE') {
            return back()->withErrors(['confirmation' => 'Please type DELETE to confirm account deletion']);
        }
        
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password']);
        }
        
        // Delete all user's pastes and files
        $user->pastes()->delete();
        $user->files()->delete();
        
        // Log out the user
        Auth::logout();
        
        // Delete the user account
        $user->delete();
        
        return redirect()->route('welcome')->with('status', 'Your account has been permanently deleted');
    }
}


