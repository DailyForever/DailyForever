<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BackupCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RecoveryController extends Controller
{
    /**
     * Show the recovery options page
     */
    public function showRecoveryOptions()
    {
        return view('auth.recovery-options');
    }

    /**
     * Show the email recovery form
     */
    public function showEmailRecovery()
    {
        return view('auth.email-recovery');
    }

    /**
     * Send recovery email
     */
    public function sendRecoveryEmail(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|max:64',
            'email' => 'required|email',
        ]);

        $user = User::where('username', $request->username)
                   ->where('email', $request->email)
                   ->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'No account found with that username and email combination.'
            ]);
        }

        // Generate secure recovery token
        $token = Str::random(32);
        $user->recovery_token = Hash::make($token);
        $user->recovery_token_expires_at = Carbon::now()->addHours(24);
        $user->save();

        // Send recovery email
        try {
            Mail::send('emails.account-recovery', [
                'username' => $user->username,
                'token' => $token,
                'expires_at' => $user->recovery_token_expires_at
            ], function ($message) use ($user) {
                $message->to($user->email)
                       ->subject('DailyForever Account Recovery');
            });

            return back()->with('success', 
                'Recovery instructions have been sent to your email address. ' .
                'Please check your inbox and follow the instructions. The link will expire in 24 hours.'
            );
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Failed to send recovery email. Please try again later.'
            ]);
        }
    }

    /**
     * Show the recovery form with token
     */
    public function showRecoveryForm(Request $request)
    {
        $token = $request->get('token');
        $username = $request->get('username');

        if (!$token || !$username) {
            return redirect()->route('auth.recovery.options')
                           ->withErrors(['token' => 'Invalid recovery link.']);
        }

        $user = User::where('username', $username)->first();
        
        if (!$user || !$user->recovery_token || !$user->recovery_token_expires_at) {
            return redirect()->route('auth.recovery.options')
                           ->withErrors(['token' => 'Invalid or expired recovery link.']);
        }

        if (Carbon::now()->greaterThan($user->recovery_token_expires_at)) {
            return redirect()->route('auth.recovery.options')
                           ->withErrors(['token' => 'Recovery link has expired.']);
        }

        if (!Hash::check($token, $user->recovery_token)) {
            return redirect()->route('auth.recovery.options')
                           ->withErrors(['token' => 'Invalid recovery link.']);
        }

        return view('auth.reset-pin', compact('username', 'token'));
    }

    /**
     * Reset PIN with email token
     */
    public function resetPinWithEmail(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'token' => 'required|string',
            'new_pin' => 'required|digits_between:4,8',
            'confirm_pin' => 'required|same:new_pin',
        ], [
            'confirm_pin.same' => 'PIN confirmation does not match.',
            'new_pin.digits_between' => 'PIN must be between 4 and 8 digits.',
        ]);

        $user = User::where('username', $request->username)->first();
        
        if (!$user || !$user->recovery_token || !$user->recovery_token_expires_at) {
            return back()->withErrors(['token' => 'Invalid recovery link.']);
        }

        if (Carbon::now()->greaterThan($user->recovery_token_expires_at)) {
            return back()->withErrors(['token' => 'Recovery link has expired.']);
        }

        if (!Hash::check($request->token, $user->recovery_token)) {
            return back()->withErrors(['token' => 'Invalid recovery link.']);
        }

        // Update PIN and clear recovery token
        $user->pin_hash = Hash::make($request->new_pin);
        $user->recovery_token = null;
        $user->recovery_token_expires_at = null;
        $user->save();

        return redirect()->route('auth.login')
                       ->with('success', 'Your PIN has been successfully reset. You can now log in with your new PIN.');
    }

    /**
     * Show the security questions recovery form
     */
    public function showSecurityQuestions()
    {
        return view('auth.security-questions-recovery');
    }

    /**
     * Verify security questions and reset PIN
     */
    public function verifySecurityQuestions(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'question1' => 'required|string',
            'answer1' => 'required|string',
            'question2' => 'required|string',
            'answer2' => 'required|string',
            'new_pin' => 'required|digits_between:4,8',
            'confirm_pin' => 'required|same:new_pin',
        ]);

        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return back()->withErrors(['username' => 'Username not found.']);
        }

        // Verify security questions
        $question1Correct = $user->security_question_1 === $request->question1 && 
                           Hash::check($request->answer1, $user->security_answer_1_hash);
        $question2Correct = $user->security_question_2 === $request->question2 && 
                           Hash::check($request->answer2, $user->security_answer_2_hash);

        if (!$question1Correct || !$question2Correct) {
            return back()->withErrors([
                'answer1' => 'One or more security answers are incorrect.'
            ]);
        }

        // Update PIN
        $user->pin_hash = Hash::make($request->new_pin);
        $user->save();

        return redirect()->route('auth.login')
                       ->with('success', 'Your PIN has been successfully reset using security questions.');
    }

    /**
     * Show the backup code recovery form
     */
    public function showBackupCodeRecovery()
    {
        return view('auth.backup-code-recovery');
    }

    /**
     * Verify backup code and reset PIN
     */
    public function verifyBackupCode(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'backup_code' => 'required|string',
            'new_pin' => 'required|digits_between:4,8',
            'confirm_pin' => 'required|same:new_pin',
        ]);

        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return back()->withErrors(['username' => 'Username not found.']);
        }

        // Verify backup code (encrypted storage)
        if (!$user->backup_code_hash || !BackupCodeService::verify($request->backup_code, $user->backup_code_hash)) {
            return back()->withErrors([
                'backup_code' => 'Invalid backup code.'
            ]);
        }

        // Update PIN and generate new backup code (encrypted)
        $user->pin_hash = Hash::make($request->new_pin);
        $generated = BackupCodeService::generateAndEncrypt();
        $newBackupCode = $generated['code'];
        $user->backup_code_hash = $generated['encrypted'];
        $user->save();

        return redirect()->route('auth.login')
                       ->with('success', 'Your PIN has been reset. Your new backup code is: ' . $newBackupCode)
                       ->with('new_backup_code', $newBackupCode);
    }
}
