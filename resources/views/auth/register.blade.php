@extends('layouts.app')

@section('title', 'Create Account - DailyForever')

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
                        <h3 class="text-sm font-medium text-yt-text" data-i18n="register.success_title">Success!</h3>
                        <p class="text-sm text-yt-text-secondary mt-1">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2" data-i18n="register.header_title">Create Your Account</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg" data-i18n="register.header_subtitle">Join DailyForever for secure, encrypted pastes</p>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('auth.register') }}" class="space-y-4" data-srp id="srpRegisterForm">
            @csrf
            
            <!-- Username Field -->
            <div class="space-y-2">
                <label for="username" class="block text-sm font-semibold text-gray-700 dark:text-gray-300" data-i18n="register.username_label">Username</label>
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
                        data-i18n-attr="placeholder" data-i18n-placeholder="register.username_placeholder" placeholder="Choose a unique username"
                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('username') border-red-500 @enderror"
                        required
                        autocomplete="username"
                        value="{{ old('username') }}"
                        minlength="3"
                        maxlength="64"
                    />
                </div>
                @error('username')
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" data-i18n="register.username_hint">3-64 characters. This will be visible to others.</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div id="passwordField" class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300" data-i18n="register.password_label">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        data-i18n-attr="placeholder" data-i18n-placeholder="register.password_placeholder" placeholder="Create a strong password"
                        class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('password') border-red-500 @enderror"
                        required
                        autocomplete="new-password"
                        minlength="8"
                    />
                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Password Strength Indicator -->
                <div id="passwordStrength" class="hidden">
                    <div class="flex items-center space-x-2 mb-2">
                        <div class="flex space-x-1">
                            <div class="w-6 h-1 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                            <div class="w-6 h-1 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                            <div class="w-6 h-1 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                            <div class="w-6 h-1 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                        </div>
                        <span id="strengthText" class="text-xs font-medium text-gray-600 dark:text-gray-400"></span>
                    </div>
                </div>
                
                @error('password')
                    <div class="flex items-center space-x-2 mt-2">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    </div>
                @else
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400" data-i18n="register.password_hint">At least 8 characters. Use a strong, unique password.</p>
                @enderror
            </div>

            <!-- PIN Field -->
            <div>
                <label for="pin" class="block text-sm font-medium text-yt-text mb-2" data-i18n="register.pin_label">PIN (4-8 digits)</label>
                <input
                    type="password"
                    id="pin"
                    name="pin"
                    data-i18n-attr="placeholder" data-i18n-placeholder="register.pin_placeholder" placeholder="Enter a 4-8 digit PIN"
                    class="input-field w-full px-4 py-3 text-lg font-mono @error('pin') border-red-500 @enderror"
                    required
                    autocomplete="one-time-code"
                    pattern="[0-9]{4,8}"
                    minlength="4"
                    maxlength="8"
                    inputmode="numeric"
                />
                @error('pin')
                    <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                @else
                    <p class="mt-1 text-xs text-yt-text-secondary" data-i18n="register.pin_hint">Choose a 4-8 digit PIN for quick access and recovery.</p>
                @enderror
            </div>

            <!-- ZKP Authentication Option -->
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-yt-text" data-i18n="register.srp.title">SRP Authentication</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="srpEnabled" class="sr-only peer" />
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        <p class="text-xs text-yt-text-secondary mt-1" data-i18n="register.srp.desc">Enable SRP (Secure Remote Password) authentication. Your password never leaves your device - only cryptographic proofs are sent to the server.</p>
                    </div>
                </div>
            </div>

            <!-- Terms and Privacy -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yt-text" data-i18n="register.privacy.title">Privacy & Security</h3>
                        <p class="text-xs text-yt-text-secondary mt-1">
                            <span data-i18n="register.privacy.text_prefix">By creating an account, you agree to our</span>
                            <a href="{{ route('legal.terms') }}" class="underline hover:no-underline text-yt-accent" data-i18n="register.privacy.terms">Terms of Service</a>
                            <span data-i18n="register.privacy.and">and</span>
                            <a href="{{ route('legal.privacy') }}" class="underline hover:no-underline text-yt-accent" data-i18n="register.privacy.privacy">Privacy Policy</a>.
                            <span data-i18n="register.privacy.text_suffix">Your data is encrypted with zero-knowledge architecture.</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-primary w-full py-3 text-lg font-medium">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span data-i18n="register.create_account">Create Account</span>
            </button>
        </form>

        <!-- Login Link -->
        <div class="text-center">
            <p class="text-sm text-yt-text-secondary">
                <span data-i18n="register.have_account">Already have an account?</span>
                <a href="{{ route('auth.login.show') }}" class="font-medium text-yt-accent hover:text-yt-accent-strong" data-i18n="register.sign_in_here">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeOffIcon = document.getElementById('eyeOffIcon');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            eyeIcon.classList.toggle('hidden');
            eyeOffIcon.classList.toggle('hidden');
        });
    }

    // Password strength indicator
    const passwordStrength = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('strengthText');
    
    if (password && passwordStrength && strengthText) {
        password.addEventListener('input', function() {
            const passwordValue = this.value;
            
            if (passwordValue.length === 0) {
                passwordStrength.classList.add('hidden');
                return;
            }
            
            passwordStrength.classList.remove('hidden');
            
            // Calculate password strength
            let score = 0;
            let feedback = [];
            
            if (passwordValue.length >= 8) score++;
            else feedback.push('at least 8 characters');
            
            if (/[a-z]/.test(passwordValue)) score++;
            else feedback.push('lowercase letters');
            
            if (/[A-Z]/.test(passwordValue)) score++;
            else feedback.push('uppercase letters');
            
            if (/[0-9]/.test(passwordValue)) score++;
            else feedback.push('numbers');
            
            if (/[^A-Za-z0-9]/.test(passwordValue)) score++;
            else feedback.push('special characters');
            
            // Update strength indicator
            const strengthBars = passwordStrength.querySelectorAll('.w-6.h-1');
            const strengthLevels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];
            
            strengthBars.forEach((bar, index) => {
                bar.className = 'w-6 h-1 rounded-full ' + (index < score ? colors[score - 1] : 'bg-gray-200 dark:bg-gray-700');
            });
            
            strengthText.textContent = strengthLevels[score - 1] || 'Very Weak';
            strengthText.className = 'text-xs font-medium ' + (score >= 3 ? 'text-green-600 dark:text-green-400' : score >= 2 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
            
            // Show feedback for weak passwords
            if (score < 3 && feedback.length > 0) {
                strengthText.textContent += ' (needs ' + feedback.slice(0, 2).join(', ') + ')';
            }
        });
    }

    // PIN validation and formatting
    const pin = document.getElementById('pin');
    if (pin) {
        pin.addEventListener('input', function(e) {
            // Only allow digits
            e.target.value = e.target.value.replace(/\D/g, '');
            
            // Limit to 8 digits
            if (e.target.value.length > 8) {
                e.target.value = e.target.value.slice(0, 8);
            }
        });

        pin.addEventListener('blur', function() {
            const value = this.value;
            if (value.length > 0 && value.length < 4) {
                this.setCustomValidity('PIN must be at least 4 digits');
            } else if (value.length > 8) {
                this.setCustomValidity('PIN must be no more than 8 digits');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Username validation
    const username = document.getElementById('username');
    if (username) {
        username.addEventListener('input', function(e) {
            // Remove any invalid characters (keep alphanumeric, underscore, hyphen)
            e.target.value = e.target.value.replace(/[^a-zA-Z0-9_-]/g, '');
        });
    }

    // SRP Registration handling
    const srpEnabled = document.getElementById('srpEnabled');
    const form = document.querySelector('form');
    const passwordField = document.getElementById('passwordField');
    const passwordInput = document.getElementById('password');
    
    // Handle SRP toggle
    if (srpEnabled && passwordField) {
        srpEnabled.addEventListener('change', function() {
            if (this.checked) {
                // SRP enabled - show password field (needed for SRP registration)
                passwordField.style.display = 'block';
                passwordInput.required = true;
                passwordInput.placeholder = 'Enter password for SRP authentication (never transmitted)';
            } else {
                // SRP disabled - show normal password field
                passwordField.style.display = 'block';
                passwordInput.required = true;
                passwordInput.placeholder = 'Enter your password';
            }
        });
    }
    
    if (srpEnabled && form) {
        form.addEventListener('submit', async function(e) {
            if (srpEnabled.checked) {
                e.preventDefault();
                
                const submitBtn = document.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                try {
                    // Check if SRP is supported
                    if (typeof SRPAuthentication === 'undefined') {
                        throw new Error('SRP authentication is not supported in this browser');
                    }
                    
                    if (!SRPAuthentication.isSupported()) {
                        throw new Error('SRP authentication is not available');
                    }
                    
                    // Show loading state
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<svg class="w-5 h-5 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Setting up SRP authentication...';
                    
                    // Get form data
                    const username = document.getElementById('username').value;
                    const password = document.getElementById('password').value;
                    const pin = document.getElementById('pin').value;
                    
                    if (!password) {
                        throw new Error('Password is required for SRP authentication');
                    }
                    
                    // Register with SRP
                    const srpResult = await SRPAuthentication.register(username, password, pin);
                    
                    if (!srpResult.success) {
                        throw new Error(srpResult.error || 'SRP registration failed');
                    }
                    
                    // Redirect to dashboard
                    window.location.href = '{{ route('user.dashboard') }}';
                    
                } catch (error) {
                    console.error('SRP registration error:', error);
                    
                    // Show error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-4';
                    errorDiv.innerHTML = `
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">SRP Registration Failed</h3>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">${error.message}</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-2">Please try again or use regular registration.</p>
                            </div>
                        </div>
                    `;
                    
                    form.insertBefore(errorDiv, form.firstChild);
                    
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


