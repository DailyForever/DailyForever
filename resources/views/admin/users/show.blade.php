@extends('layouts.app')

@section('title', 'User Details - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-yt-text">{{ $user->username }}</h1>
                <p class="text-yt-text-secondary">User Account Details</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Back to Users</a>
                <a href="{{ route('admin.users.activity', $user) }}" class="btn-primary">View Activity</a>
            </div>
        </div>
        
        <!-- Privacy Notice -->
        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-4 mt-4">
            <h3 class="font-semibold text-yt-accent mb-2">Privacy Notice</h3>
            <p class="text-sm text-yt-text">
                Administrative access is limited to account metadata only. We cannot access encrypted content due to our zero-knowledge architecture. 
                All administrative actions are logged for audit purposes.
            </p>
        </div>
    </div>

    <!-- User Info Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Basic Info -->
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Basic Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-yt-text-secondary">Username</label>
                    <div class="text-yt-text">{{ $user->username }}</div>
                </div>
                @if($user->name)
                <div>
                    <label class="text-sm font-medium text-yt-text-secondary">Full Name</label>
                    <div class="text-yt-text">{{ $user->name }}</div>
                </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-yt-text-secondary">Member Since</label>
                    <div class="text-yt-text">{{ $user->created_at->format('M j, Y \a\t g:i A') }}</div>
                </div>
                <div>
                    <label class="text-sm font-medium text-yt-text-secondary">Last Activity</label>
                    <div class="text-yt-text">{{ $stats['last_activity']->format('M j, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Account Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-yt-text-secondary">Admin Status</span>
                    <div class="flex items-center space-x-2">
                        @if($user->is_admin)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-accent text-white">
                                Admin
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-text-disabled text-white">
                                Regular User
                            </span>
                        @endif
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                                @csrf
                                <button type="submit" class="btn-secondary px-2 py-1 text-xs">
                                    {{ $user->is_admin ? 'Remove Admin' : 'Make Admin' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-yt-text-secondary">2FA Status</span>
                    <div class="flex items-center space-x-2">
                        @if($user->two_factor_enabled)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-success text-white">
                                Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-warning text-white">
                                Disabled
                            </span>
                        @endif
                        <form method="POST" action="{{ route('admin.users.toggle-2fa', $user) }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-secondary px-2 py-1 text-xs">
                                {{ $user->two_factor_enabled ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-yt-text-secondary">Suspension Status</span>
                    <div class="flex items-center space-x-2">
                        @if($user->isSuspended())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-warning text-white">
                                Suspended
                            </span>
                            @if($user->suspended_until)
                                <span class="text-xs text-yt-text-secondary">
                                    Until {{ $user->suspended_until->format('M j, Y') }}
                                </span>
                            @else
                                <span class="text-xs text-yt-text-secondary">Permanent</span>
                            @endif
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-success text-white">
                                Active
                            </span>
                        @endif
                    </div>
                </div>
                @if($user->suspension_reason)
                <div>
                    <label class="text-sm font-medium text-yt-text-secondary">Suspension Reason</label>
                    <div class="text-yt-text text-sm">{{ $user->suspension_reason }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Pastes</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['total_pastes']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Active Pastes</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['active_pastes']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Removed Pastes</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['removed_pastes']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Files</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['total_files']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Views</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['total_views']) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Storage Used</span>
                    <span class="text-yt-text font-medium">{{ number_format($stats['storage_used'] / 1024 / 1024, 2) }} MB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="content-card p-6 mb-6">
        <h3 class="text-lg font-medium text-yt-text mb-4">Administrative Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Reset Password -->
            <div>
                <h4 class="font-medium text-yt-text mb-2">Reset Password</h4>
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="space-y-2">
                    @csrf
                    <input type="password" name="new_password" placeholder="New Password" 
                           class="input-field w-full" required>
                    <input type="password" name="new_password_confirmation" placeholder="Confirm Password" 
                           class="input-field w-full" required>
                    <button type="submit" class="btn-warning w-full">Reset Password</button>
                </form>
            </div>

            <!-- Suspend User -->
            @if($user->id !== auth()->id())
            <div>
                <h4 class="font-medium text-yt-text mb-2">Suspend User</h4>
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="space-y-2">
                    @csrf
                    <input type="text" name="reason" placeholder="Reason for suspension" 
                           class="input-field w-full">
                    <input type="number" name="duration" placeholder="Duration (days, leave empty for permanent)" 
                           class="input-field w-full" min="1" max="365">
                    <button type="submit" class="btn-warning w-full">Suspend User</button>
                </form>
            </div>

            <!-- Delete User -->
            <div>
                <h4 class="font-medium text-yt-text mb-2">Delete User</h4>
                <form method="POST" action="{{ route('admin.users.delete', $user) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" 
                      class="space-y-2">
                    @csrf
                    <input type="text" name="confirmation" placeholder="Type DELETE to confirm" 
                           class="input-field w-full" required>
                    <button type="submit" class="btn-danger w-full">Delete User</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Pastes -->
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Recent Pastes</h3>
            @if($recentPastes->count() > 0)
                <div class="space-y-3">
                    @foreach($recentPastes as $paste)
                    <div class="flex items-center justify-between p-3 bg-yt-hover rounded">
                        <div>
                            <div class="font-medium text-yt-text">{{ $paste->identifier }}</div>
                            <div class="text-sm text-yt-text-secondary">
                                {{ $paste->views }} views • {{ $paste->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($paste->is_removed)
                                <span class="text-xs text-yt-warning">Removed</span>
                            @else
                                <span class="text-xs text-yt-success">Active</span>
                            @endif
                            <a href="{{ route('paste.show', $paste->identifier) }}" 
                               class="btn-secondary px-2 py-1 text-xs">View</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-yt-text-secondary">No pastes found.</p>
            @endif
        </div>

        <!-- Recent Files -->
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Recent Files</h3>
            @if($recentFiles->count() > 0)
                <div class="space-y-3">
                    @foreach($recentFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-yt-hover rounded">
                        <div>
                            <div class="font-medium text-yt-text">{{ $file->original_filename }}</div>
                            <div class="text-sm text-yt-text-secondary">
                                {{ number_format($file->size_bytes / 1024, 1) }} KB • {{ $file->views }} views
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-yt-text-secondary">{{ $file->created_at->diffForHumans() }}</span>
                            <a href="{{ route('files.show', $file->identifier) }}" 
                               class="btn-secondary px-2 py-1 text-xs">View</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-yt-text-secondary">No files found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
