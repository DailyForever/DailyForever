@extends('layouts.app')

@section('title', 'Two‑Factor Verification - DailyForever')

@section('content')
<div class="max-w-md mx-auto">
    <div class="content-card p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 mx-auto bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-yt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-yt-text" data-i18n="twofa.title">Two‑Factor Authentication</h1>
            <p class="text-yt-text-secondary" data-i18n="twofa.subtitle">Enter the 6‑digit code from your authenticator app</p>
        </div>

        <form method="POST" action="{{ route('auth.2fa.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-sm font-medium text-yt-text mb-2" data-i18n="twofa.code_label">Authentication Code</label>
                <input type="text" id="code" name="code" pattern="\d{6}" inputmode="numeric" autocomplete="one-time-code" data-i18n-attr="placeholder" data-i18n-placeholder="twofa.placeholder" placeholder="123456" class="input-field w-full px-4 py-3 text-lg font-mono" required />
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-lg font-medium" data-i18n="twofa.verify">Verify</button>
        </form>
        <div class="mt-3 text-sm text-center">
            <a href="{{ route('auth.login.show') }}" class="text-yt-accent" data-i18n="twofa.back_to_login">Back to login</a>
        </div>
    </div>
</div>
@endsection
