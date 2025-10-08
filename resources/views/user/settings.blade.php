@extends('layouts.app')

@section('title', 'Settings - DailyForever')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-8 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-yt-text">Account Settings</h1>
                <p class="text-yt-text-secondary">Manage your account security and preferences</p>
            </div>
        </div>

        @if(session('status'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                <p class="text-sm text-yt-text">{{ session('status') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Email Management Section -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-yt-text mb-2">Email Recovery</h2>
                    <p class="text-sm text-yt-text-secondary">Add an email address for secure account recovery</p>
                </div>
                
                <div class="rounded-lg border border-yt-border p-6">
                    <form method="POST" action="{{ route('settings.email.update') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-yt-text mb-2">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                value="{{ auth()->user()->email }}"
                                placeholder="Enter your email address"
                                class="input-field w-full"
                            />
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="text-xs text-yt-text-secondary">
                            Adding an email allows you to recover your account if you forget your PIN. 
                            Your email is only used for account recovery and is never shared.
                        </p>
                        <button class="btn-primary px-4 py-2 text-sm">Update Email</button>
                    </form>
                </div>
            </div>

            <!-- Encryption Key Storage Section -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-yt-text mb-2">Encryption Key Storage</h2>
                    <p class="text-sm text-yt-text-secondary">Control whether your encryption keys are stored for convenient access</p>
                </div>
                
                <div class="rounded-lg border border-yt-border p-6">
                    <form method="POST" action="{{ route('settings.encryption-keys.update') }}" class="space-y-4">
                        @csrf
                        <div class="flex items-center space-x-3">
                            <input 
                                type="checkbox" 
                                name="store_encryption_keys" 
                                value="1" 
                                id="store_encryption_keys"
                                {{ auth()->user()->store_encryption_keys ? 'checked' : '' }}
                                class="w-4 h-4 text-yt-accent bg-yt-bg border-yt-border rounded focus:ring-yt-accent focus:ring-2"
                            />
                            <label for="store_encryption_keys" class="text-sm font-medium text-yt-text">
                                Store encryption keys for convenient access
                            </label>
                        </div>
                        <p class="text-xs text-yt-text-secondary">
                            When enabled, your encryption keys are stored so you can view your pastes without needing the URL fragment. 
                            This makes it easier to manage your pastes while maintaining the same security level.
                        </p>
                        <button class="btn-primary px-4 py-2 text-sm">Update Preference</button>
                    </form>
                </div>
            </div>

            <!-- Two-Factor Authentication Section -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-yt-text mb-2">Two‑Factor Authentication (TOTP)</h2>
                    <p class="text-sm text-yt-text-secondary">Add an extra layer of security to your account</p>
                </div>

                @if(auth()->user()->two_factor_enabled)
                    <div class="rounded-lg border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 p-6">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="font-medium text-yt-text">2FA is enabled</h3>
                                <p class="text-sm text-yt-text-secondary">Your account is protected with two-factor authentication</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('settings.2fa.disable') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="btn-secondary px-4 py-2 text-sm">Disable 2FA</button>
                        </form>
                    </div>
                @else
                    <div class="rounded-lg border border-yt-border p-6 space-y-6">
                        <div>
                            <h3 class="font-medium text-yt-text mb-3">Setup Instructions</h3>
                            <ol class="list-decimal list-inside text-sm text-yt-text-secondary space-y-2">
                                <li>Install an authenticator app (Google Authenticator, Authy, Aegis)</li>
                                <li>Scan the QR code below with your app</li>
                                <li>Enter the 6-digit code from your app to confirm</li>
                            </ol>
                        </div>
                        
                        <div class="flex flex-col items-center space-y-6">
                            <!-- QR Code Section -->
                            <div class="text-center">
                                <div class="border-2 border-yt-border rounded-xl p-6 bg-white dark:bg-gray-800 inline-block">
                                    <img id="qrImage" src="" alt="QR Code" class="mx-auto" style="display: none; width: 200px; height: 200px;" />
                                    <div id="qrLoading" class="w-48 h-48 flex items-center justify-center text-yt-text-secondary">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yt-accent"></div>
                                    </div>
                                </div>
                                <p class="text-sm text-yt-text-secondary mt-3">Scan this QR code with your authenticator app</p>
                                <button id="genBtn" type="button" class="btn-secondary mt-3 px-4 py-2 text-sm">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Generate QR Code
                                </button>
                            </div>
                            
                            <!-- Verification Section -->
                            <div class="w-full max-w-sm">
                                <h4 class="font-medium text-yt-text mb-3 text-center">Verify Setup</h4>
                                <form method="POST" action="{{ route('settings.2fa.enable') }}" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-yt-text mb-2 text-center">Enter 6-digit code from your app</label>
                                        <input name="code" inputmode="numeric" pattern="\d{6}" placeholder="123456" class="input-field w-full px-4 py-3 font-mono text-lg text-center tracking-widest" required />
                                        @error('code')
                                            <p class="text-sm text-yt-error mt-2 text-center">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button class="btn-primary w-full py-3 text-lg font-medium">
                                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Enable 2FA
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Password Section -->
            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-yt-text mb-2">Password</h2>
                    <p class="text-sm text-yt-text-secondary">Update your account password</p>
                </div>
                
                <div class="rounded-lg border border-yt-border p-6">
                    <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-yt-text mb-2">Current Password</label>
                            <input type="password" name="current_password" class="input-field w-full px-3 py-2" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-yt-text mb-2">New Password</label>
                            <input type="password" name="new_password" class="input-field w-full px-3 py-2" required minlength="8" />
                        </div>
                        <button class="btn-primary w-full py-2">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Management -->
        <div class="mt-12">
            <div class="border-t border-yt-border pt-8">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-semibold text-yt-text mb-2">Account Management</h2>
                        <p class="text-sm text-yt-text-secondary">Manage your account data and preferences</p>
                    </div>
                    
                    <div class="rounded-lg border border-yt-border bg-yt-bg p-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="font-medium text-yt-text mb-2">Delete Account</h3>
                                <p class="text-sm text-yt-text-secondary mb-4">
                                    Permanently remove your account and all associated data. This action cannot be undone.
                                </p>
                            </div>
                            
                            <form method="POST" action="{{ route('settings.delete-account') }}" class="space-y-4" onsubmit="return confirmDeleteAccount()">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-yt-text mb-2">
                                        Type <strong>DELETE</strong> to confirm
                                    </label>
                                    <input 
                                        type="text" 
                                        name="confirmation" 
                                        class="input-field w-full px-3 py-2" 
                                        placeholder="DELETE"
                                        required 
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-yt-text mb-2">Current Password</label>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        class="input-field w-full px-3 py-2" 
                                        required 
                                    />
                                </div>
                                <button 
                                    type="submit" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                >
                                    Delete Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const genBtn = document.getElementById('genBtn');
    const qrImage = document.getElementById('qrImage');
    const qrLoading = document.getElementById('qrLoading');
    let isLoading = false;

    async function generateQR() {
        if (isLoading) return;
        isLoading = true;
        
        try {
            // Show loading state
            qrImage.style.display = 'none';
            qrLoading.style.display = 'flex';
            
            // Fetch secret and QR code from server
            const response = await fetch('{{ route('settings.2fa.otpauth') }}', { 
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            
            const data = await response.json();
            console.log('OTP data received:', data);
            
            // Display the server-generated QR code
            if (data.qr_code) {
                qrImage.src = data.qr_code;
                qrImage.style.display = 'block';
                qrLoading.style.display = 'none';
                console.log('QR code loaded successfully');
            } else {
                throw new Error('No QR code data received');
            }
            
        } catch (error) {
            console.error('QR generation failed:', error);
            
            // Show error state
            qrLoading.innerHTML = `
                <div class="text-center">
                    <div class="text-yt-error text-sm">Failed to load</div>
                    <div class="text-yt-text-secondary text-xs mt-1">Click Generate</div>
                </div>
            `;
        } finally {
            isLoading = false;
        }
    }

    // Generate QR on button click
    if (genBtn) {
        genBtn.addEventListener('click', generateQR);
    }

    // Auto-generate on page load
    generateQR();
});

function confirmDeleteAccount() {
    const confirmation = document.querySelector('input[name="confirmation"]').value;
    if (confirmation !== 'DELETE') {
        alert('Please type DELETE to confirm account deletion');
        return false;
    }
    
    return confirm(
        'Are you sure you want to delete your account?\n\n' +
        'This will permanently remove:\n' +
        '• Your account and settings\n' +
        '• All your pastes and files\n' +
        '• All encryption keys\n' +
        '• All associated data\n\n' +
        'This action cannot be undone.'
    );
}
</script>
@endsection