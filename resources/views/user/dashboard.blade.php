@extends('layouts.app')

@section('title', 'Dashboard - DailyForever')

@section('content')
<div class="w-full">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-yt-text" data-i18n="user.dashboard.title" data-i18n-doc-title="user.dashboard.doc_title">Dashboard</h1>
                <p class="text-sm text-yt-text-secondary mt-1"><span data-i18n="user.dashboard.welcome">Welcome back,</span> {{ auth()->user()->username }}</p>
            </div>
            <div class="flex items-center">
                <div class="text-right">
                    <div class="text-xs sm:text-sm text-yt-text-secondary" data-i18n="user.dashboard.account_status">Account Status</div>
                    <div class="flex items-center justify-end space-x-2 mt-1">
                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yt-text" data-i18n="user.dashboard.status.active">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="user.dashboard.stats.total_pastes">Total Pastes</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_pastes']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $stats['active_pastes'] }} <span data-i18n="user.dashboard.stats.active">active</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="user.dashboard.stats.files_uploaded">Files Uploaded</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_files']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ number_format($stats['storage_used'] / (1024*1024), 1) }} <span data-i18n="common.units.mb">MB</span> <span data-i18n="user.dashboard.stats.used">used</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="user.dashboard.stats.this_week">This Week</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ $stats['pastes_this_week'] + $stats['files_this_week'] }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $stats['pastes_this_week'] }} <span data-i18n="common.metrics.pastes">pastes</span>, {{ $stats['files_this_week'] }} <span data-i18n="user.dashboard.stats.files">files</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="user.dashboard.stats.security">Security</p>
                    @php
                        $u = auth()->user();
                        $hasSRP = method_exists($u, 'hasSRPEnabled') ? $u->hasSRPEnabled() : false;
                        if ($u->two_factor_enabled) {
                            $securityLabel = $hasSRP ? '2FA + SRP' : '2FA';
                            $securityDesc = $hasSRP ? 'Two-factor enabled, SRP zero-knowledge auth' : 'Two-factor enabled';
                        } else {
                            $securityLabel = $hasSRP ? 'SRP' : 'PIN';
                            $securityDesc = $hasSRP ? 'SRP zero-knowledge authentication' : 'PIN recovery';
                        }
                    @endphp
                    <p class="text-2xl font-semibold text-yt-text">{{ $securityLabel }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $securityDesc }}</p>
                </div>
            </div>
        </div>

        <!-- Prekeys Stats -->
        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="user.dashboard.prekeys.title">Prekeys</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['prekeys_available'] ?? 0) }} <span data-i18n="user.dashboard.prekeys.available">available</span></p>
                    <p class="text-xs text-yt-text-secondary">{{ number_format($stats['prekeys_used'] ?? 0) }} <span data-i18n="user.dashboard.prekeys.used">used</span> • {{ number_format($stats['prekeys_total'] ?? 0) }} <span data-i18n="user.dashboard.prekeys.total">total</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
        <!-- Quick Actions -->
        <div class="content-card p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="user.dashboard.quick.title">Quick Actions</h2>
            <div class="space-y-3">
                <a href="{{ route('paste.create') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.quick.create_paste">Create Paste</h3>
                            <p class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.quick.create_paste_desc">Encrypted, zero-knowledge sharing</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('files.create') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.quick.upload_file">Upload File</h3>
                            <p class="text-sm text-yt-text-secondary"> <span data-i18n="user.dashboard.quick.upload_file_desc">Up to 150MB, client-side encrypted</span></p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('pastes.mine') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.quick.my_pastes">My Pastes</h3>
                            <p class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.quick.my_pastes_desc">View and manage your pastes</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('user.files') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.quick.my_files">My Files</h3>
                            <p class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.quick.my_files_desc">Manage your uploaded files</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>

                <a href="{{ route('prekeys.index') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <!-- lock/key stylized icon -->
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.quick.prekeys">Prekeys</h3>
                            <p class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.quick.prekeys_desc">Generate & manage one-time keys</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Account Management -->
        <div class="content-card p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-yt-text mb-4" data-i18n="user.dashboard.account.title">Account Management</h2>
            <div class="space-y-3">
                <a href="{{ route('settings.index') }}" class="flex items-center justify-between p-4 border border-yt-border rounded-lg hover:bg-yt-surface transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-yt-text" data-i18n="user.dashboard.account.settings">Account Settings</h3>
                            <p class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.account.settings_desc">Password, 2FA, encryption preferences</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-yt-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-6 sm:mb-8">
        <!-- Recent Pastes -->
        <div class="content-card p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-yt-text" data-i18n="user.dashboard.recent_pastes.title">Recent Pastes</h3>
                <a href="{{ route('pastes.mine') }}" class="text-sm text-yt-accent hover:underline" data-i18n="user.dashboard.recent_pastes.view_all">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentPastes as $paste)
                    <div class="flex items-center justify-between p-3 bg-yt-surface rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yt-text">{{ $paste->identifier }}</p>
                                <p class="text-xs text-yt-text-secondary">{{ $paste->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($paste->is_private)
                                <span class="px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded" data-i18n="paste.mine.private">Private</span>
                            @endif
                            <span class="text-xs text-yt-text-secondary">{{ $paste->views }} <span data-i18n="common.metrics.views">views</span></span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 text-yt-text-disabled mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-yt-text-secondary text-sm" data-i18n="user.dashboard.recent_pastes.empty_title">No pastes yet</p>
                        <a href="{{ route('paste.create') }}" class="text-yt-accent text-sm hover:underline" data-i18n="user.dashboard.recent_pastes.empty_cta">Create your first paste</a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Files -->
        <div class="content-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-yt-text" data-i18n="user.dashboard.recent_files.title">Recent Files</h3>
                <a href="{{ route('user.files') }}" class="text-sm text-yt-accent hover:underline" data-i18n="user.dashboard.recent_files.view_all">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($recentFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-yt-surface rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yt-text truncate max-w-32" title="{{ $file->original_filename }}">{{ $file->original_filename }}</p>
                                <p class="text-xs text-yt-text-secondary">{{ $file->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-yt-text-secondary">{{ number_format($file->size_bytes / 1024, 1) }} <span data-i18n="common.units.kb">KB</span></p>
                            <p class="text-xs text-yt-text-secondary">{{ $file->views }} <span data-i18n="common.metrics.downloads">downloads</span></p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <svg class="w-12 h-12 text-yt-text-disabled mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <p class="text-yt-text-secondary text-sm" data-i18n="user.dashboard.recent_files.empty_title">No files yet</p>
                        <a href="{{ route('files.create') }}" class="text-yt-accent text-sm hover:underline" data-i18n="user.dashboard.recent_files.empty_cta">Upload your first file</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activity Chart - YouTube Analytics Style -->
    <div class="content-card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-yt-text" data-i18n="user.dashboard.activity.title">Activity Overview</h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-yt-accent rounded-full"></div>
                    <span class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.activity.legend.pastes_created">Pastes Created</span>
                </div>
                <div class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.activity.legend.last_7_days">
                    Last 7 days
                </div>
            </div>
        </div>
        
        @if($activity->count() > 0)
            @php
                $maxValue = max(1, $activity->max('total'));
                $chartHeight = 200;
            @endphp
            
            <div class="relative">
                <!-- Chart Container -->
                <div class="relative h-48 bg-yt-surface rounded-lg p-4">
                    <!-- Y-axis labels -->
                    <div class="absolute left-0 top-0 h-full flex flex-col justify-between text-xs text-yt-text-secondary">
                        <span>{{ $maxValue }}</span>
                        <span>{{ round($maxValue * 0.75) }}</span>
                        <span>{{ round($maxValue * 0.5) }}</span>
                        <span>{{ round($maxValue * 0.25) }}</span>
                        <span>0</span>
                    </div>
                    
                    <!-- Chart Area -->
                    <div class="ml-8 h-full flex items-end space-x-1">
                        @foreach($activity as $index => $point)
                            @php
                                $height = ($point['total'] / $maxValue) * 100;
                                $isToday = $index === $activity->count() - 1;
                            @endphp
                            <div class="flex flex-col items-center flex-1 group">
                                <!-- Bar -->
                                <div class="w-full bg-yt-accent hover:bg-yt-accent/80 rounded-t transition-all duration-200 group-hover:opacity-80 relative" 
                                     style="height: {{ max(2, $height) }}%">
                                    <!-- Tooltip on hover -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-yt-text text-yt-bg text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                        {{ $point['total'] }} <span data-i18n="common.metrics.pastes">pastes</span>
                                    </div>
                                </div>
                                
                                <!-- Day label -->
                                <div class="mt-2 text-xs text-yt-text-secondary {{ $isToday ? 'font-semibold text-yt-accent' : '' }}">
                                    {{ $point['label'] }}
                                </div>
                                
                                <!-- Value -->
                                <div class="text-xs font-medium text-yt-text mt-1">
                                    {{ $point['total'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Grid lines -->
                    <div class="absolute inset-0 ml-8">
                        <div class="h-full flex flex-col justify-between">
                            @for($i = 0; $i <= 4; $i++)
                                <div class="border-t border-yt-border opacity-30"></div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-yt-surface rounded-lg">
                    <div class="text-2xl font-bold text-yt-text">{{ $activity->sum('total') }}</div>
                    <div class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.activity.summary.total_pastes">Total Pastes</div>
                </div>
                <div class="text-center p-4 bg-yt-surface rounded-lg">
                    <div class="text-2xl font-bold text-yt-text">{{ round($activity->avg('total'), 1) }}</div>
                    <div class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.activity.summary.daily_average">Daily Average</div>
                </div>
                <div class="text-center p-4 bg-yt-surface rounded-lg">
                    <div class="text-2xl font-bold text-yt-text">{{ $activity->max('total') }}</div>
                    <div class="text-sm text-yt-text-secondary" data-i18n="user.dashboard.activity.summary.peak_day">Peak Day</div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-yt-text-disabled mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h4 class="text-lg font-medium text-yt-text mb-2" data-i18n="user.dashboard.activity.empty.title">No Activity Yet</h4>
                <p class="text-yt-text-secondary mb-4" data-i18n="user.dashboard.activity.empty.desc">Start creating pastes to see your activity analytics</p>
                <a href="{{ route('paste.create') }}" class="inline-flex items-center px-4 py-2 bg-yt-accent text-white rounded-lg hover:bg-yt-accent/90 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span data-i18n="user.dashboard.activity.empty.cta">Create Your First Paste</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection