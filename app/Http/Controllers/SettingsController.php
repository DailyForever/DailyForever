<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\TwoFactorService;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', [
            'user' => $user,
        ]);
    }

    public function generateTwoFactorSecret(Request $request)
    {
        $user = $request->user();
        $twoFactorService = new TwoFactorService();
        
        // Generate a proper 2FA secret
        $secret = $twoFactorService->generateSecret();
        $user->two_factor_secret = $secret;
        $user->two_factor_enabled = false;
        $user->two_factor_confirmed_at = null;
        $user->save();

        // Generate QR code URL
        $qrCodeUrl = $twoFactorService->getQRCodeUrl(
            config('app.name', 'DailyForever'),
            $user->email ?? $user->username . '@dailyforever.com',
            $secret
        );

        return response()->json([
            'success' => true,
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    public function confirmTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $user = $request->user();
        if (!$user->two_factor_secret) {
            return back()->withErrors(['code' => 'No secret generated. Please start again.']);
        }

        $twoFactorService = new TwoFactorService();
        
        // Properly verify the TOTP code
        if (!$twoFactorService->verifyCode($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();

        // No server-side logging per no-logs policy

        return redirect()->route('settings.index')->with('success', 'Two‑Factor Authentication enabled successfully.');
    }

    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
        return back()->with('success', 'Two‑Factor Authentication disabled.');
    }
}


