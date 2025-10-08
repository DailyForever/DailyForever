@extends('layouts.app')

@section('title', 'Account Recovery - DailyForever')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-6">Account Recovery</h1>
        <p class="text-yt-text-secondary mb-8">
            If you've lost access to your account, we provide multiple secure recovery methods. 
            Choose the option that works best for you.
        </p>

        <div class="space-y-6">
            <!-- Email Recovery -->
            <div class="border border-yt-border rounded-lg p-6 hover:bg-yt-surface transition-colors">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Email Recovery</h3>
                        <p class="text-yt-text-secondary mb-4">
                            If you provided an email address in your settings, we can send you a secure recovery link.
                        </p>
                        <a href="{{ route('auth.recovery.email') }}" class="btn-primary">
                            Recover with Email
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Questions -->
            <div class="border border-yt-border rounded-lg p-6 hover:bg-yt-surface transition-colors">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Security Questions</h3>
                        <p class="text-yt-text-secondary mb-4">
                            If you set up security questions during registration, you can use them to reset your PIN.
                        </p>
                        <a href="{{ route('auth.recovery.security-questions') }}" class="btn-primary">
                            Recover with Security Questions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Backup Code -->
            <div class="border border-yt-border rounded-lg p-6 hover:bg-yt-surface transition-colors">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yt-text mb-2">Backup Code</h3>
                        <p class="text-yt-text-secondary mb-4">
                            If you saved a backup code during registration, you can use it to reset your PIN.
                        </p>
                        <a href="{{ route('auth.recovery.backup-code') }}" class="btn-primary">
                            Recover with Backup Code
                        </a>
                    </div>
                </div>
            </div>

            <!-- No Recovery Options -->
            <div class="border border-red-500/30 bg-red-900/10 rounded-lg p-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-400 mb-2">No Recovery Options Available</h3>
                        <p class="text-yt-text-secondary mb-4">
                            If you don't have any of the above recovery methods set up, your account cannot be recovered. 
                            This is by design to ensure maximum security and privacy.
                        </p>
                        <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-4">
                            <p class="text-sm text-red-300">
                                <strong>Important:</strong> DailyForever uses zero-knowledge architecture. We cannot 
                                access your account or reset your PIN without proper authentication. This ensures 
                                your privacy but means you must set up recovery methods in advance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('auth.login') }}" class="text-yt-accent hover:underline">
                ← Back to Login
            </a>
        </div>
    </div>
</div>
@endsection
