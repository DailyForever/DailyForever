@extends('layouts.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-yt-text" data-i18n="admin.users.index.title" data-i18n-doc-title="admin.users.index.doc_title">User Management</h1>
        <p class="text-yt-text-secondary" data-i18n="admin.users.index.subtitle">Manage users, permissions, and account status</p>
        
        <!-- Privacy Notice -->
        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-4 mt-4">
            <h3 class="font-semibold text-yt-accent mb-2" data-i18n="admin.users.index.notice.title">Privacy Notice</h3>
            <p class="text-sm text-yt-text" data-i18n="admin.users.index.notice.text">
                Administrative access is limited to account metadata only. We cannot access encrypted content due to our zero-knowledge architecture.
                All administrative actions are logged for audit purposes.
            </p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="content-card p-4">
            <div class="text-2xl font-bold text-yt-text">{{ number_format($stats['total_users']) }}</div>
            <div class="text-sm text-yt-text-secondary" data-i18n="admin.users.index.stats.total_users">Total Users</div>
        </div>
        <div class="content-card p-4">
            <div class="text-2xl font-bold text-yt-accent">{{ number_format($stats['admin_users']) }}</div>
            <div class="text-sm text-yt-text-secondary" data-i18n="admin.users.index.stats.admin_users">Admin Users</div>
        </div>
        <div class="content-card p-4">
            <div class="text-2xl font-bold text-yt-success">{{ number_format($stats['two_factor_enabled']) }}</div>
            <div class="text-sm text-yt-text-secondary" data-i18n="admin.users.index.stats.two_factor_enabled">2FA Enabled</div>
        </div>
        <div class="content-card p-4">
            <div class="text-2xl font-bold text-yt-warning">{{ number_format($stats['users_today']) }}</div>
            <div class="text-sm text-yt-text-secondary" data-i18n="admin.users.index.stats.new_today">New Today</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="content-card p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="admin.users.index.filters.search">Search</label>
                <input type="text" name="q" value="{{ request('q') }}"
                       class="input-field w-full" data-i18n-attr="placeholder" data-i18n-placeholder="admin.users.index.filters.search_placeholder" placeholder="Username or name">
            </div>
            <div>
                <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="admin.users.index.filters.admin_status">Admin Status</label>
                <select name="admin" class="input-field w-full">
                    <option value="" data-i18n="admin.users.index.filters.all_users">All Users</option>
                    <option value="yes" {{ request('admin') === 'yes' ? 'selected' : '' }} data-i18n="admin.users.index.filters.admins_only">Admins Only</option>
                    <option value="no" {{ request('admin') === 'no' ? 'selected' : '' }} data-i18n="admin.users.index.filters.regular_users">Regular Users</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="admin.users.index.filters.two_factor_status">2FA Status</label>
                <select name="two_factor" class="input-field w-full">
                    <option value="" data-i18n="admin.users.index.filters.all_users">All Users</option>
                    <option value="enabled" {{ request('two_factor') === 'enabled' ? 'selected' : '' }} data-i18n="admin.users.index.filters.twofa_enabled">2FA Enabled</option>
                    <option value="disabled" {{ request('two_factor') === 'disabled' ? 'selected' : '' }} data-i18n="admin.users.index.filters.twofa_disabled">2FA Disabled</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full" data-i18n="admin.users.index.buttons.filter">Filter</button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="content-card">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-yt-border">
                        <th class="text-left py-3 px-4 font-medium text-yt-text" data-i18n="admin.users.index.th.user">User</th>
                        <th class="text-left py-3 px-4 font-medium text-yt-text" data-i18n="admin.users.index.th.status">Status</th>
                        <th class="text-left py-3 px-4 font-medium text-yt-text" data-i18n="admin.users.index.th.activity">Activity</th>
                        <th class="text-left py-3 px-4 font-medium text-yt-text" data-i18n="admin.users.index.th.joined">Joined</th>
                        <th class="text-left py-3 px-4 font-medium text-yt-text" data-i18n="admin.users.index.th.actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="border-b border-yt-border hover:bg-yt-hover">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-yt-accent rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-medium text-yt-text">{{ $user->username }}</div>
                                    @if($user->name)
                                        <div class="text-sm text-yt-text-secondary">{{ $user->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="space-y-1">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-accent text-white" data-i18n="admin.users.index.badges.admin">
                                        Admin
                                    </span>
                                @endif
                                @if($user->two_factor_enabled)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-success text-white" data-i18n="admin.users.index.badges.twofa">2FA</span>
                                @endif
                                @if($user->isSuspended())
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-warning text-white" data-i18n="admin.users.index.badges.suspended">Suspended</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-sm text-yt-text">
                                {{ number_format($user->pastes_count) }} <span data-i18n="admin.users.index.activity.pastes">pastes</span>
                            </div>
                            <div class="text-sm text-yt-text">
                                {{ number_format($user->files_count) }} <span data-i18n="admin.users.index.activity.files">files</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-sm text-yt-text">
                                {{ $user->created_at->format('M j, Y') }}
                            </div>
                            <div class="text-xs text-yt-text-secondary">
                                {{ $user->created_at->diffForHumans() }}
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="btn-secondary px-3 py-1 text-xs" data-i18n="common.buttons.view">View</a>
                                <a href="{{ route('admin.users.activity', $user) }}" 
                                   class="btn-secondary px-3 py-1 text-xs" data-i18n="admin.users.index.buttons.activity">Activity</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-4 text-center text-yt-text-secondary" data-i18n="admin.users.index.empty">
                            No users found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-yt-border">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
