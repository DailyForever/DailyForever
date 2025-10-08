<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prekey;
use App\Services\BackupCodeService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:64|unique:users,username|regex:/^[a-zA-Z0-9_-]+$/',
            'password' => 'required|string|min:8',
            'pin' => 'required|digits_between:4,8',
        ], [
            'username.required' => 'Username is required.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.max' => 'Username must not exceed 64 characters.',
            'username.unique' => 'This username is already taken. Please choose another.',
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'pin.required' => 'PIN is required.',
            'pin.digits_between' => 'PIN must be between 4 and 8 digits.',
        ]);

        // Generate and encrypt backup code
        $backupCodeData = BackupCodeService::generateAndEncrypt();
        $backupCode = $backupCodeData['code'];
        $encryptedBackupCode = $backupCodeData['encrypted'];
        
        $user = User::create([
            'name' => $request->username,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'pin_hash' => Hash::make($request->pin),
            'backup_code_hash' => $encryptedBackupCode,
        ]);

        Auth::login($user);
        
        // No server-side logging per no-logs policy
        
        return redirect()->route('paste.create')
            ->with('success', 'Account created successfully! Welcome to DailyForever.')
            ->with('backup_code', $backupCode);
    }

 

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'nullable|string',
            'backup_code' => 'nullable|string',
        ], [
            'username.required' => 'Username is required.',
        ]);

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return back()->withErrors(['username' => 'Invalid username or password. Please check your credentials and try again.']);
        }

        // Check if using backup code
        if ($request->backup_code) {
            if (!$user->backup_code_hash || !BackupCodeService::verify($request->backup_code, $user->backup_code_hash)) {
                return back()->withErrors(['backup_code' => 'Invalid backup code. Please check your code and try again.']);
            }
        } else {
            // Regular password login
            if (!$request->password) {
                return back()->withErrors(['username' => 'Password is required. Please check your credentials and try again.']);
            }
            
            // Check if user has ZKP enabled (no password hash)
            if ($user->hasZKPEnabled()) {
                return back()->withErrors(['username' => 'This account previously used ZKP login, which is no longer supported. Please use account recovery to set a new password.']);
            }
            
            if (!$user->password || !Hash::check($request->password, $user->password)) {
                return back()->withErrors(['username' => 'Invalid username or password. Please check your credentials and try again.']);
            }
        }
        if ($user->two_factor_enabled) {
            // Defer full login until TOTP is verified
            session(['2fa:user_id' => $user->id]);
            return redirect()->route('auth.2fa.show');
        }

        Auth::login($user);
        
        // Only generate new backup code if using backup code login
        if ($request->backup_code) {
            $newBackupCodeData = BackupCodeService::generateAndEncrypt();
            $newBackupCode = $newBackupCodeData['code'];
            $encryptedNewBackupCode = $newBackupCodeData['encrypted'];
            
            $user->backup_code_hash = $encryptedNewBackupCode;
            $user->save();
            
            return redirect()->route('pastes.mine')
                ->with('success', 'Login successful! Your backup code has been updated.')
                ->with('backup_code', $newBackupCode);
        }
        
        return redirect()->route('pastes.mine')->with('success', 'Welcome back! You have been successfully logged in.');
    }

    public function showTwoFactor()
    {
        if (!session('2fa:user_id')) {
            return redirect()->route('auth.login.show');
        }
        return view('auth.2fa');
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $userId = session('2fa:user_id');
        if (!$userId) {
            return redirect()->route('auth.login.show');
        }
        $user = User::find($userId);
        if (!$user || !$user->two_factor_enabled || !$user->two_factor_secret) {
            return redirect()->route('auth.login.show');
        }
        $twoFactorService = new TwoFactorService();
        if (!$twoFactorService->verifyCode($user->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }
        // No server-side logging per no-logs policy
        
        session()->forget('2fa:user_id');
        Auth::login($user);
        return redirect()->intended(route('pastes.mine'));
    }


    public function showPinRecovery()
    {
        return view('auth.recover');
    }

    public function startRecovery(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:64',
        ]);
        
        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return back()->withErrors(['username' => 'Username not found. Please check your spelling and try again.']);
        }
        
        // Generate a more user-friendly token (shorter, with separators)
        $token = $this->generateRecoveryToken();
        
        // Store only a hash of the token
        $user->recovery_token = \Illuminate\Support\Facades\Hash::make($token);
        $user->recovery_token_expires_at = Carbon::now()->addMinutes(15);
        $user->save();
        
        return back()->with([
            'token' => $token,
            'recovery_username' => $user->username,
            'success' => 'Recovery token generated successfully! It will expire in 15 minutes.'
        ]);
    }

    public function completeRecovery(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:64',
            'token' => 'required|string',
            'new_pin' => 'required|digits_between:4,8',
            'confirm_pin' => 'required|same:new_pin',
        ], [
            'confirm_pin.same' => 'PIN confirmation does not match.',
            'new_pin.digits_between' => 'PIN must be between 4 and 8 digits.',
        ]);
        
        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return back()->withErrors(['username' => 'Username not found.']);
        }
        
        if (!$user->recovery_token || !\Illuminate\Support\Facades\Hash::check($request->token, $user->recovery_token)) {
            return back()->withErrors(['token' => 'Invalid recovery token. Please check your token and try again.']);
        }
        
        if (!$user->recovery_token_expires_at || Carbon::now()->greaterThan($user->recovery_token_expires_at)) {
            return back()->withErrors(['token' => 'Recovery token has expired. Please generate a new one.']);
        }
        
        // Update the PIN
        $user->pin_hash = Hash::make($request->new_pin);
        $user->recovery_token = null;
        $user->recovery_token_expires_at = null;
        $user->save();
        
        // Redirect to password reset step instead of login
        return redirect()->route('auth.recover.verify-password')->with([
            'success' => 'PIN updated successfully! Please set a new password to complete recovery.',
            'recovery_username' => $user->username
        ]);
    }

    public function verifyPasswordAndCompleteRecovery(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:64',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ], [
            'confirm_password.same' => 'Password confirmation does not match.',
            'new_password.min' => 'Password must be at least 8 characters.',
        ]);
        
        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return back()->withErrors(['username' => 'Username not found.']);
        }
        
        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();
        
        return redirect()->route('auth.login.show')->with('success', 'Recovery completed! Your password has been updated. Please log in with your new credentials.');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('paste.create');
    }

    /**
     * Generate a user-friendly recovery token
     */
    private function generateRecoveryToken(): string
    {
        // Generate 8 random bytes and present as 4 groups of 4 uppercase hex chars: XXXX-XXXX-XXXX-XXXX
        $hex = strtoupper(bin2hex(random_bytes(8))); // 16 hex characters
        return substr($hex, 0, 4) . '-' . substr($hex, 4, 4) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4);
    }

 

    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        
        $user = Auth::user();
        
        // Get user statistics
        $stats = [
            'total_pastes' => $user->pastes()->count(),
            'active_pastes' => $user->pastes()->where('is_removed', false)->count(),
            'total_files' => $user->files()->count(),
            'storage_used' => $user->files()->sum('size_bytes'),
            'pastes_this_week' => $user->pastes()->where('created_at', '>=', now()->subWeek())->count(),
            'files_this_week' => $user->files()->where('created_at', '>=', now()->subWeek())->count(),
        ];

        // Prekeys stats
        $prekeysTotal = Prekey::where('user_id', $user->id)->count();
        $prekeysAvailable = Prekey::where('user_id', $user->id)->whereNull('used_at')->count();
        $prekeysUsed = max(0, $prekeysTotal - $prekeysAvailable);
        $stats['prekeys_total'] = $prekeysTotal;
        $stats['prekeys_available'] = $prekeysAvailable;
        $stats['prekeys_used'] = $prekeysUsed;
        
        // Get recent activity
        $recentPastes = $user->pastes()
            ->where('is_removed', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
            
        $recentFiles = $user->files()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        
        // Get activity for the last 7 days
        $activity = $user->pastes()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => \Carbon\Carbon::parse($row->day)->format('M j'),
                    'total' => (int) $row->total,
                ];
            });
        
        return view('user.dashboard', compact('stats', 'recentPastes', 'recentFiles', 'activity'));
    }
}


