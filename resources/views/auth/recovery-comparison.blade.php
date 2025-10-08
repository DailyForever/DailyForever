@extends('layouts.app')

@section('title', 'Recovery System Comparison - DailyForever')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="content-card p-8">
        <h1 class="text-3xl font-bold text-yt-text mb-6">Recovery System Enhancement</h1>
        <p class="text-yt-text-secondary mb-8">
            How the new multi-method recovery system addresses PIN loss risks compared to the current system.
        </p>

        <!-- Current System Problems -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-red-400 mb-6">❌ Current System Problems</h2>
            
            <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-red-300 mb-4">Single Point of Failure</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-white text-sm">1</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-300">Username-Only Recovery</h4>
                            <p class="text-yt-text-secondary text-sm">User must remember exact username to start recovery</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-white text-sm">2</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-300">Catch-22 Situation</h4>
                            <p class="text-yt-text-secondary text-sm">User needs account access to generate recovery token</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-white text-sm">3</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-300">High Risk of Permanent Loss</h4>
                            <p class="text-yt-text-secondary text-sm">If user loses PIN and can't access account → Account lost forever</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <span class="text-white text-sm">4</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-300">No Backup Methods</h4>
                            <p class="text-yt-text-secondary text-sm">Single recovery method creates vulnerability</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-yt-surface rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yt-text mb-3">Current Recovery Flow</h3>
                <div class="flex items-center justify-between space-x-4 text-sm">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <span class="text-red-600 dark:text-red-400 font-bold">1</span>
                        </div>
                        <div class="text-yt-text-secondary">Enter Username</div>
                    </div>
                    <div class="flex-1 h-0.5 bg-red-300"></div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <span class="text-red-600 dark:text-red-400 font-bold">2</span>
                        </div>
                        <div class="text-yt-text-secondary">Generate Token</div>
                    </div>
                    <div class="flex-1 h-0.5 bg-red-300"></div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <span class="text-red-600 dark:text-red-400 font-bold">3</span>
                        </div>
                        <div class="text-yt-text-secondary">Reset PIN</div>
                    </div>
                </div>
                <div class="mt-4 text-center text-red-400 text-sm">
                    <strong>Problem:</strong> Step 2 requires account access → Catch-22!
                </div>
            </div>
        </div>

        <!-- New System Solutions -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-green-400 mb-6">✅ New Multi-Method Recovery System</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Email Recovery -->
                <div class="border border-green-500/30 bg-green-900/10 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-300">Email Recovery</h3>
                    </div>
                    <ul class="text-sm text-yt-text-secondary space-y-2">
                        <li>• Secure email verification</li>
                        <li>• 24-hour token expiration</li>
                        <li>• Zero-knowledge maintained</li>
                        <li>• User-friendly process</li>
                    </ul>
                </div>

                <!-- Security Questions -->
                <div class="border border-green-500/30 bg-green-900/10 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-300">Security Questions</h3>
                    </div>
                    <ul class="text-sm text-yt-text-secondary space-y-2">
                        <li>• Two-factor questions</li>
                        <li>• Hashed answers stored</li>
                        <li>• Offline recovery option</li>
                        <li>• No internet required</li>
                    </ul>
                </div>

                <!-- Backup Code -->
                <div class="border border-green-500/30 bg-green-900/10 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-green-300">Backup Code</h3>
                    </div>
                    <ul class="text-sm text-yt-text-secondary space-y-2">
                        <li>• One-time use codes</li>
                        <li>• Cryptographically secure</li>
                        <li>• Generates new code after use</li>
                        <li>• Physical backup option</li>
                    </ul>
                </div>
            </div>

            <div class="bg-green-900/20 border border-green-500/30 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-300 mb-4">New Recovery Flow</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">1</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-300">Choose Recovery Method</h4>
                            <p class="text-yt-text-secondary text-sm">User selects from multiple available options</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">2</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-300">Verify Identity</h4>
                            <p class="text-yt-text-secondary text-sm">Secure verification using chosen method</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white text-sm font-bold">3</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-green-300">Reset PIN Securely</h4>
                            <p class="text-yt-text-secondary text-sm">PIN reset while maintaining zero-knowledge architecture</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Mitigation -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-yt-accent mb-6">🛡️ Risk Mitigation</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-yt-surface rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yt-text mb-3">Before (High Risk)</h3>
                    <ul class="text-yt-text-secondary space-y-2">
                        <li>❌ Single recovery method</li>
                        <li>❌ Catch-22 situation</li>
                        <li>❌ High risk of permanent loss</li>
                        <li>❌ No backup options</li>
                        <li>❌ User dependency on remembering username</li>
                    </ul>
                </div>
                
                <div class="bg-yt-surface rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-yt-text mb-3">After (Low Risk)</h3>
                    <ul class="text-yt-text-secondary space-y-2">
                        <li>✅ Multiple recovery methods</li>
                        <li>✅ No catch-22 situations</li>
                        <li>✅ Redundant recovery options</li>
                        <li>✅ Email, questions, backup codes</li>
                        <li>✅ User-friendly recovery process</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Implementation Status -->
        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yt-accent mb-4">Implementation Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-yt-text mb-2">✅ Completed</h4>
                    <ul class="text-sm text-yt-text-secondary space-y-1">
                        <li>• RecoveryController with all methods</li>
                        <li>• Email recovery system</li>
                        <li>• Security questions recovery</li>
                        <li>• Backup code recovery</li>
                        <li>• Recovery options page</li>
                        <li>• All routes configured</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-yt-text mb-2">🔄 Next Steps</h4>
                    <ul class="text-sm text-yt-text-secondary space-y-1">
                        <li>• Update User model for new fields</li>
                        <li>• Create database migrations</li>
                        <li>• Add recovery setup to registration</li>
                        <li>• Create email templates</li>
                        <li>• Test all recovery methods</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('auth.recovery.options') }}" class="btn-primary">
                Try New Recovery System
            </a>
        </div>
    </div>
</div>
@endsection
