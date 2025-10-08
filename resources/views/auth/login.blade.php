@extends('layouts.app')

@section('title', 'Login - DailyForever')

@section('content')
<div class="max-w-md mx-auto">
    <div class="content-card p-8 space-y-6">
        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yt-text" data-i18n="login.success_title">Success!</h3>
                        <p class="text-sm text-yt-text-secondary mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Message -->
        @if(session('message'))
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yt-text" data-i18n="login.notice_title">Notice</h3>
                        <p class="text-sm text-yt-text-secondary mt-1">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="w-20 h-20 mx-auto rounded-2xl flex items-center justify-center border border-yt-border bg-yt-elevated/40">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2" data-i18n="login.header_welcome">Welcome Back</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg" data-i18n="login.header_subtitle">Access your secure workspace</p>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('auth.login') }}" class="space-y-4" data-srp id="srpLoginForm">
            @csrf
            <div class="space-y-2">
                <label for="username" class="block text-sm font-semibold text-gray-700 dark:text-gray-300" data-i18n="login.username_label">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        data-i18n-attr="placeholder" data-i18n-placeholder="login.username_placeholder" placeholder="Enter your username" 
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                        required 
                        autocomplete="username"
                        value="{{ old('username') }}"
                        style="font-size: 16px;"
                    />
                </div>
                @error('username')
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @enderror
            </div>

            <!-- ZKP Login Option -->
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl p-5 shadow-sm">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white" data-i18n="login.srp_title">SRP Authentication</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="srpLoginEnabled" class="sr-only peer" />
                                <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-gradient-to-r peer-checked:from-purple-600 peer-checked:to-indigo-600"></div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400" data-i18n="login.srp_desc">Maximum security: Your password never leaves your device. Uses SRP-6a protocol for zero-knowledge authentication.</p>
                    </div>
                </div>
            </div>
            
        <!-- Smart Login Input -->
        <div class="space-y-4">
            <div>
                <label for="loginInput" class="block text-sm font-semibold text-gray-700 dark:text-gray-300" data-i18n="login.password_or_code">Password or Backup Code</label>
                <div class="relative">
                    <input 
                        type="password" 
                        id="loginInput" 
                        name="loginInput" 
                        data-i18n-attr="placeholder" data-i18n-placeholder="login.placeholder_default" placeholder="Enter your password or 16-character backup code" 
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 font-mono" 
                        maxlength="16"
                        autocomplete="current-password"
                        style="font-size: 16px;"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg id="passwordIcon" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <svg id="backupCodeIcon" class="h-5 w-5 text-purple-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" id="togglePasswordBtn" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <span id="inputHint" data-i18n="login.input_hint_default">Enter your password or backup code above</span>
                    </p>
                    <div class="flex items-center space-x-2">
                        <div id="inputTypeIndicator" class="flex items-center space-x-1 text-xs">
                            <div id="passwordIndicator" class="flex items-center space-x-1 text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                <span class="text-gray-600 dark:text-gray-400" data-i18n="login.password_label">Password</span>
                            </div>
                            <div id="backupCodeIndicator" class="flex items-center space-x-1 text-xs bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded-full hidden">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                <span class="text-purple-700 dark:text-purple-300" data-i18n="login.backup_code_label">Backup Code</span>
                            </div>
                        </div>
                    </div>
                </div>
                @error('password')
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @enderror
                @error('backup_code')
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @enderror
            </div>
        </div>

        <!-- Hidden inputs for form submission -->
        <input type="hidden" id="password" name="password" />
        <input type="hidden" id="backup_code" name="backup_code" />

            <button type="submit" class="btn-primary w-full py-3 text-lg font-medium">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span data-i18n="login.sign_in">Sign In</span>
            </button>
            
            <!-- Security Features -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1" data-i18n="login.security_features.title">Security Features</h4>
                        <ul class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                            <li>• <span data-i18n="login.security_features.list.e2e">End-to-end encryption for all data</span></li>
                            <li>• <span data-i18n="login.security_features.list.srp">SRP zero-knowledge authentication</span></li>
                            <li>• <span data-i18n="login.security_features.list.recovery">Secure password recovery options</span></li>
                            <li>• <span data-i18n="login.security_features.list.totp">Two-factor authentication support</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    💡 <span data-i18n="login.tip_text">Tip: Enter your password normally, or paste a 16-character backup code</span>
                </p>
            </div>
        </form>

        <!-- Recovery Link -->
            <div class="text-center">
                <a href="{{ route('auth.recover.show') }}" class="text-sm text-yt-accent hover:text-yt-accent-strong font-medium" data-i18n="login.recover_link">Forgot your PIN? Recover your account</a>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <p class="text-sm text-yt-text-secondary">
                    <span data-i18n="login.no_account">Don't have an account?</span>
                    <a href="{{ route('auth.register.show') }}" class="font-medium text-yt-accent hover:text-yt-accent-strong" data-i18n="login.create_one">Create one here</a>
                </p>
            </div>
    </div>
</div>

<script type="text/plain" data-disabled="moved-to-module">
document.addEventListener('DOMContentLoaded', function() {
    const loginInput = document.getElementById('loginInput');
    const passwordInput = document.getElementById('password');
    const backupCodeInput = document.getElementById('backup_code');
    const inputHint = document.getElementById('inputHint');
    const passwordIndicator = document.getElementById('passwordIndicator');
    const backupCodeIndicator = document.getElementById('backupCodeIndicator');
    const passwordIcon = document.getElementById('passwordIcon');
    const backupCodeIcon = document.getElementById('backupCodeIcon');

    // Smart input detection
    function detectInputType(value) {
        // Backup code: exactly 16 characters, alphanumeric only
        if (value.length === 16 && /^[A-Z0-9]+$/.test(value)) {
            return 'backup_code';
        }
        // Password: any other input
        return 'password';
    }

    // Update UI based on input type
    function updateInputType(type) {
        if (type === 'backup_code') {
            // Show backup code indicator
            passwordIndicator.classList.add('hidden');
            backupCodeIndicator.classList.remove('hidden');
            inputHint.textContent = 'Backup code detected - this will change after login';
            
            // Update input styling
            loginInput.classList.add('border-yt-accent');
            loginInput.classList.remove('border-yt-border');
            
            // Update icons
            passwordIcon.classList.add('hidden');
            backupCodeIcon.classList.remove('hidden');
            
            // Show backup code as text (visible)
            loginInput.type = 'text';
        } else {
            // Show password indicator
            passwordIndicator.classList.remove('hidden');
            backupCodeIndicator.classList.add('hidden');
            inputHint.textContent = 'Password detected - enter your account password';
            
            // Update input styling
            loginInput.classList.remove('border-yt-accent');
            loginInput.classList.add('border-yt-border');
            
            // Update icons
            passwordIcon.classList.remove('hidden');
            backupCodeIcon.classList.add('hidden');
            
            // Hide password (masked)
            loginInput.type = 'password';
        }
    }

    // Handle input changes
    loginInput.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // Detect input type first
        const inputType = detectInputType(value);
        
        // Only auto-format for backup codes (uppercase, alphanumeric only)
        if (inputType === 'backup_code') {
            value = value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            e.target.value = value;
        }
        
        // Update UI based on input type
        updateInputType(inputType);
        
        // Clear hidden inputs
        passwordInput.value = '';
        backupCodeInput.value = '';
    });

    // Handle form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const value = loginInput.value;
        const inputType = detectInputType(value);
        
        // Set the appropriate hidden input
        if (inputType === 'backup_code') {
            backupCodeInput.value = value;
            passwordInput.value = '';
        } else {
            passwordInput.value = value;
            backupCodeInput.value = '';
        }
    });

    // Handle paste events
    loginInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            const value = e.target.value;
            const inputType = detectInputType(value);
            updateInputType(inputType);
        }, 10);
    });

    // Initial state
    updateInputType('password');

    // Password visibility toggle
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    
    if (togglePasswordBtn && loginInput) {
        togglePasswordBtn.addEventListener('click', function() {
            const type = loginInput.getAttribute('type') === 'password' ? 'text' : 'password';
            loginInput.setAttribute('type', type);
            
            // Update icon
            const icon = this.querySelector('svg');
            if (type === 'text') {
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });
    }

    // SRP Login handling
    const srpLoginEnabled = document.getElementById('srpLoginEnabled');
    const loginForm = document.querySelector('form');
    const loginInputField = document.getElementById('loginInput')?.parentElement?.parentElement;
    
    // Wait for SRP module to load (guard against dynamic import race)
    async function ensureSrpReady(timeoutMs = 3000) {
        const sleep = (ms) => new Promise(r => setTimeout(r, ms));
        const start = Date.now();
        while (Date.now() - start < timeoutMs) {
            if (typeof window.SRPAuthentication !== 'undefined') {
                try {
                    if (!window.SRPAuthentication.isSupported() && typeof window.SRPAuthentication.initialize === 'function') {
                        await window.SRPAuthentication.initialize();
                    }
                } catch (_) {}
                return true;
            }
            await sleep(50);
        }
        return false;
    }

    // Handle SRP login toggle
    if (srpLoginEnabled && loginInputField) {
        srpLoginEnabled.addEventListener('change', function() {
            if (this.checked) {
                // SRP enabled - show password field for SRP
                loginInputField.style.display = 'block';
                const loginInput = document.getElementById('loginInput');
                loginInput.placeholder = 'Enter your password for SRP authentication';
                loginInput.type = 'password';
            } else {
                // SRP disabled - show normal password/backup code field
                loginInputField.style.display = 'block';
                const loginInput = document.getElementById('loginInput');
                loginInput.placeholder = 'Enter your password or 16-character backup code';
                updateInputType('password');
            }
        });
    }
    
    if (srpLoginEnabled && loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            if (srpLoginEnabled.checked) {
                e.preventDefault();
                
                const submitBtn = document.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                try {
                    // Check if SRP is supported
                    const ready = await ensureSrpReady(3000);
                    if (!ready || typeof SRPAuthentication === 'undefined') {
                        throw new Error('SRP authentication is not supported in this browser');
                    }
                    if (!SRPAuthentication.isSupported()) {
                        throw new Error('SRP authentication is not available');
                    }
                    
                    // Get form data
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('loginInput').value;
                    
                    if (!username || !password) {
                        throw new Error('Username and password are required');
                    }
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Initiating SRP authentication...';
                    
                    // Initiate SRP login
                    const initiateResult = await SRPAuthentication.initiateLogin(username);
                    
                    if (!initiateResult.success) {
                        throw new Error(initiateResult.error || 'Failed to initiate SRP authentication');
                    }
                    
                    // Generate login proof
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Generating proof...';
                    
                    const loginResult = await SRPAuthentication.completeLogin(username, password, initiateResult.data);
                    
                    if (!loginResult.success) {
                        throw new Error(loginResult.error || 'SRP authentication failed');
                    }
                    
                    // Redirect using server-provided URL if available
                    const redirectUrl = (loginResult.data && loginResult.data.redirect) ? loginResult.data.redirect : '{{ route('user.dashboard') }}';
                    window.location.href = redirectUrl;
                    
                } catch (error) {
                    console.error('SRP login error:', error);
                    // Graceful fallback: if SRP isn't enabled for this user, submit the form normally
                    try {
                        const msg = (error && error.message) ? String(error.message) : '';
                        if (msg.includes('SRP authentication not enabled')) {
                            if (srpLoginEnabled) srpLoginEnabled.checked = false;
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            // Submit form with password/backup code for regular login
                            const visiblePwd = document.getElementById('loginInput').value || '';
                            const hiddenPwd = document.getElementById('password');
                            const hiddenCode = document.getElementById('backup_code');
                            if (hiddenPwd) hiddenPwd.value = visiblePwd;
                            if (hiddenCode) hiddenCode.value = '';
                            if (loginForm.requestSubmit) {
                                loginForm.requestSubmit();
                            } else {
                                loginForm.submit();
                            }
                            return;
                        }
                    } catch (_) {}
                    
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4';
                    errorDiv.innerHTML = `
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">SRP Login Failed</h3>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">${error.message}</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2">Please try again or use regular login.</p>
                            </div>
                        </div>
                    `;
                    
                    loginForm.insertBefore(errorDiv, loginForm.firstChild);
                    
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
    }
});
</script>
@endsection


