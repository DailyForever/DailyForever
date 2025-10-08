@extends('layouts.app')

@section('title', 'Recover Account - DailyForever')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="content-card p-8 space-y-8">
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-16 h-16 mx-auto bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-yt-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-yt-text">Recover Your Account</h1>
            <p class="text-yt-text-secondary">Reset your PIN using your recovery token</p>
        </div>

        <!-- Step Indicator -->
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-medium">1</div>
                <span class="text-sm font-medium text-yt-accent">Get Token</span>
            </div>
            <div class="w-8 h-px bg-gray-300 dark:bg-gray-600"></div>
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-yt-text-secondary flex items-center justify-center text-sm font-medium">2</div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Reset PIN</span>
            </div>
        </div>

        <!-- Step 1: Get Recovery Token -->
        <div id="step-1" class="space-y-6">
            <div class="text-center">
                <h2 class="text-lg font-semibold text-yt-text mb-2">Step 1: Get Recovery Token</h2>
                <p class="text-sm text-yt-text-secondary">Enter your username to generate a recovery token</p>
            </div>

            <form method="POST" action="{{ route('auth.recover.start') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-yt-text mb-2">
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="Enter your username" 
                        class="input-field w-full px-4 py-3 text-lg" 
                        required 
                        autocomplete="username"
                        value="{{ old('username') }}"
                    />
                    @error('username')
                        <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3 text-lg font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Generate Recovery Token
                </button>
            </form>

            @if(session('token'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-yt-text">Recovery Token Generated</h3>
                            <p class="text-sm text-yt-text-secondary mt-1">
                                Your recovery token is valid for 15 minutes. Copy it and proceed to step 2.
                            </p>
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-yt-text mb-1">Recovery Token:</label>
                                <div class="flex items-center space-x-2">
                                    <code class="flex-1 bg-white dark:bg-gray-800 border border-green-300 dark:border-green-700 rounded px-3 py-2 text-sm font-mono text-yt-text break-all">
                                        {{ session('token') }}
                                    </code>
                                    <button 
                                        type="button" 
                                        onclick="copyToken()" 
                                        class="btn-secondary px-3 py-2 text-xs"
                                        title="Copy to clipboard"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Step 2: Reset PIN -->
        <div id="step-2" class="space-y-6 {{ session('token') ? '' : 'hidden' }}">
            <div class="text-center">
                <h2 class="text-lg font-semibold text-yt-text mb-2">Step 2: Reset Your PIN</h2>
                <p class="text-sm text-yt-text-secondary">Enter your recovery token and set a new PIN</p>
            </div>

            <form method="POST" action="{{ route('auth.recover.complete') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="recovery-username" class="block text-sm font-medium text-yt-text mb-2">
                        Username
                    </label>
                    <input 
                        type="text" 
                        id="recovery-username" 
                        name="username" 
                        placeholder="Enter your username" 
                        class="input-field w-full px-4 py-3 text-lg" 
                        required 
                        autocomplete="username"
                        value="{{ old('username', session('recovery_username')) }}"
                    />
                    @error('username')
                        <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="token" class="block text-sm font-medium text-yt-text mb-2">
                        Recovery Token
                    </label>
                    <input 
                        type="text" 
                        id="token" 
                        name="token" 
                        placeholder="Paste your recovery token here" 
                        class="input-field w-full px-4 py-3 text-lg font-mono" 
                        required 
                        value="{{ old('token') }}"
                    />
                    @error('token')
                        <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_pin" class="block text-sm font-medium text-yt-text mb-2">
                        New PIN (4-8 digits)
                    </label>
                    <input 
                        type="password" 
                        id="new_pin" 
                        name="new_pin" 
                        placeholder="Enter your new PIN" 
                        class="input-field w-full px-4 py-3 text-lg font-mono" 
                        required 
                        pattern="\d{4,8}"
                        maxlength="8"
                        autocomplete="new-password"
                    />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Choose a 4-8 digit PIN that you'll remember
                    </p>
                    @error('new_pin')
                        <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="confirm_pin" class="block text-sm font-medium text-yt-text mb-2">
                        Confirm New PIN
                    </label>
                    <input 
                        type="password" 
                        id="confirm_pin" 
                        name="confirm_pin" 
                        placeholder="Confirm your new PIN" 
                        class="input-field w-full px-4 py-3 text-lg font-mono" 
                        required 
                        pattern="\d{4,8}"
                        maxlength="8"
                        autocomplete="new-password"
                    />
                    @error('confirm_pin')
                        <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary w-full py-3 text-lg font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reset PIN
                </button>
            </form>
        </div>

        <!-- Step 3: Set New Password (shown after PIN reset) -->
        @if(session('success') && session('recovery_username'))
            <div class="mt-8 p-4 bg-yt-success/20 border border-yt-success rounded-lg">
                <p class="text-yt-success text-sm font-medium">{{ session('success') }}</p>
            </div>
            
            <div class="space-y-6">
                <div class="text-center">
                    <h2 class="text-lg font-semibold text-yt-text mb-2">Step 3: Set New Password</h2>
                    <p class="text-sm text-yt-text-secondary">Complete your recovery by setting a new password</p>
                </div>
                
                <form method="POST" action="{{ route('auth.recover.verify-password') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="username" value="{{ session('recovery_username') }}">
                    
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-yt-text mb-2">
                            New Password
                        </label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            placeholder="Enter your new password" 
                            class="input-field w-full px-4 py-3 text-lg" 
                            required 
                            minlength="8"
                            autocomplete="new-password"
                        />
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Password must be at least 8 characters long
                        </p>
                        @error('new_password')
                            <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-yt-text mb-2">
                            Confirm New Password
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            placeholder="Confirm your new password" 
                            class="input-field w-full px-4 py-3 text-lg" 
                            required 
                            minlength="8"
                            autocomplete="new-password"
                        />
                        @error('confirm_password')
                            <p class="mt-1 text-sm text-yt-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn-primary w-full py-3 text-lg font-medium">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Complete Recovery
                    </button>
                </form>
            </div>
        @endif

        <!-- Help Section -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-yt-accent mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-yt-text">Need Help?</h3>
                    <ul class="mt-2 text-sm text-yt-text-secondary space-y-1">
                        <li>• Recovery tokens expire after 15 minutes</li>
                        <li>• Your PIN must be 4-8 digits long</li>
                        <li>• Make sure to copy the recovery token exactly</li>
                        <li>• If you don't have a recovery token, contact support</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('auth.login.show') }}" class="text-sm text-yt-text-secondary hover:text-gray-900 dark:hover:text-white">
                ← Back to Login
            </a>
        </div>
    </div>
</div>

<script>
function copyToken() {
    const tokenElement = document.querySelector('code');
    const token = tokenElement.textContent;
    
    navigator.clipboard.writeText(token).then(() => {
        // Show success feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        button.classList.add('bg-green-600', 'text-white');
        button.classList.remove('btn-secondary');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('bg-green-600', 'text-white');
            button.classList.add('btn-secondary');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy token: ', err);
        alert('Failed to copy token. Please copy it manually.');
    });
}

// Auto-fill username in step 2 if available
document.addEventListener('DOMContentLoaded', function() {
    const step1Username = document.getElementById('username');
    const step2Username = document.getElementById('recovery-username');
    
    if (step1Username && step2Username && step1Username.value) {
        step2Username.value = step1Username.value;
    }
    
    // Show step 2 if token is available
    if (document.querySelector('code')) {
        document.getElementById('step-2').classList.remove('hidden');
        // Update step indicator
        const step2Indicator = document.querySelector('.flex.items-center.space-x-4 .w-8.h-8.bg-gray-300');
        if (step2Indicator) {
            step2Indicator.classList.remove('bg-gray-300', 'text-gray-600');
            step2Indicator.classList.add('bg-blue-600', 'text-white');
        }
    }
});

// PIN validation
document.getElementById('new_pin')?.addEventListener('input', function(e) {
    // Only allow digits
    e.target.value = e.target.value.replace(/\D/g, '');
    
    // Check if PINs match
    const confirmPin = document.getElementById('confirm_pin');
    if (confirmPin && confirmPin.value) {
        validatePinMatch();
    }
});

document.getElementById('confirm_pin')?.addEventListener('input', function(e) {
    // Only allow digits
    e.target.value = e.target.value.replace(/\D/g, '');
    validatePinMatch();
});

function validatePinMatch() {
    const newPin = document.getElementById('new_pin').value;
    const confirmPin = document.getElementById('confirm_pin').value;
    
    if (confirmPin && newPin !== confirmPin) {
        document.getElementById('confirm_pin').classList.add('border-red-500');
    } else {
        document.getElementById('confirm_pin').classList.remove('border-red-500');
    }
}
</script>
@endsection