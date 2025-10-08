@extends('layouts.app')

@section('title', 'Admin Dashboard - DailyForever')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Hero / overview -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-3xl font-semibold text-yt-text" data-i18n="admin.dashboard.title" data-i18n-doc-title="admin.dashboard.doc_title">Admin Command Center</h1>
            <p class="text-yt-text-secondary" data-i18n="admin.dashboard.subtitle">Monitor platform health, enforce policy, and manage DailyForever content in real time.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.analytics') }}" class="btn-primary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.analytics">Analytics</a>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.manage_users">Manage Users</a>
            <a href="{{ route('admin.pastes.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.manage_pastes">Manage Pastes</a>
            <a href="{{ route('admin.files.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.manage_files">Manage Files</a>
            <a href="{{ route('admin.support.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.support_reports">Support Reports</a>
            <a href="{{ route('admin.posts.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.dashboard.buttons.manage_posts">Manage Posts</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 stagger-children">
        <div class="content-card p-5 space-y-2">
            <span class="text-yt-text-secondary text-xs uppercase tracking-wide" data-i18n="admin.dashboard.stats.total_pastes">Total Pastes</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-semibold text-yt-text">{{ number_format($stats['total_pastes']) }}</span>
                <span class="text-xs text-yt-text-secondary">{{ number_format($stats['pastes_today']) }} <span data-i18n="admin.dashboard.stats.today">today</span></span>
            </div>
        </div>
        <div class="content-card p-5 space-y-2">
            <span class="text-yt-text-secondary text-xs uppercase tracking-wide" data-i18n="admin.dashboard.stats.removed_pastes">Removed Pastes</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-semibold text-yt-text">{{ number_format($stats['removed_pastes']) }}</span>
                <span class="text-xs text-yt-text-secondary">{{ number_format($stats['active_pastes']) }} <span data-i18n="admin.dashboard.stats.active">active</span></span>
            </div>
        </div>
        <div class="content-card p-5 space-y-2">
            <span class="text-yt-text-secondary text-xs uppercase tracking-wide" data-i18n="admin.dashboard.stats.storage_used">Storage Used</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-semibold text-yt-text">{{ number_format($stats['storage_used'] / (1024*1024), 1) }} MB</span>
                <span class="text-xs text-yt-text-secondary"><span data-i18n="admin.dashboard.stats.across">Across</span> {{ number_format($stats['total_files']) }} <span data-i18n="admin.dashboard.stats.files">files</span></span>
            </div>
        </div>
        <div class="content-card p-5 space-y-2">
            <span class="text-yt-text-secondary text-xs uppercase tracking-wide" data-i18n="admin.dashboard.stats.user_security">User Security</span>
            <div class="flex items-end justify-between">
                <span class="text-3xl font-semibold text-yt-text">{{ number_format($stats['total_users']) }}</span>
                <span class="text-xs text-yt-text-secondary"><span data-i18n="admin.dashboard.stats.twofa">2FA:</span> {{ number_format($stats['two_factor_enabled']) }}</span>
            </div>
        </div>
    </div>

    <!-- Activity & flagged -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 content-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-yt-text" data-i18n="admin.dashboard.activity.title">Pastes Created (7 days)</h2>
                <span class="text-xs text-yt-text-secondary" data-i18n="admin.dashboard.activity.utc">UTC time</span>
            </div>
            <div class="grid grid-cols-7 gap-3">
                @foreach($activity as $point)
                    <div class="flex flex-col items-center space-y-2">
                        <div class="content-card w-full h-32 flex items-end justify-center">
                            <div class="bg-yt-accent rounded-lg w-6" style="height: {{ $activityMax ? max(6, ($point['total'] / $activityMax) * 100) : 6 }}%"></div>
                        </div>
                        <span class="text-xs text-yt-text-secondary text-center">{{ $point['label'] }}</span>
                        <span class="text-xs text-yt-text">{{ $point['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="content-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-yt-text" data-i18n="admin.dashboard.flagged.title">Recently Flagged</h2>
                <span class="text-xs text-yt-text-secondary" data-i18n="admin.dashboard.flagged.subtitle">Auto-removed or admin takedown</span>
            </div>
            <div class="space-y-3">
                @forelse($flaggedPastes as $paste)
                    <div class="border border-yt-border rounded-lg p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-yt-text">{{ $paste->identifier }}</span>
                            <span class="text-xs text-yt-text-secondary">{{ optional($paste->removed_at)->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-yt-text-secondary mt-1"><span data-i18n="admin.dashboard.flagged.reason_label">Reason:</span>
                            @if($paste->removed_reason)
                                {{ $paste->removed_reason }}
                            @else
                                <span data-i18n="admin.pastes.index.reason_none">No reason provided</span>
                            @endif
                        </p>
                        <p class="text-xs text-yt-text-secondary mt-1"><span data-i18n="admin.dashboard.flagged.owner_label">Owner:</span>
                            @if($paste->user)
                                {{ $paste->user->username }}
                            @else
                                <span data-i18n="common.user.anonymous">Anonymous</span>
                            @endif
                        </p>
                        <div class="mt-3 flex gap-2">
                            <form action="{{ route('admin.viewlimit', $paste) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="number" min="1" name="view_limit" class="input-field px-2 py-1 text-xs w-20" data-i18n-attr="placeholder" data-i18n-placeholder="admin.dashboard.inputs.limit_placeholder" placeholder="Limit" />
                                <button class="btn-secondary px-3 py-1 text-xs" data-i18n="admin.dashboard.controls.set">Set</button>
                            </form>
                            <form action="{{ route('admin.takedown', $paste) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="reason" class="input-field px-2 py-1 text-xs w-32" data-i18n-attr="placeholder" data-i18n-placeholder="admin.dashboard.inputs.reason_placeholder" placeholder="Reason" />
                                <button class="btn-secondary px-3 py-1 text-xs">@if($paste->is_removed)<span data-i18n="admin.dashboard.controls.update">Update</span>@else<span data-i18n="admin.dashboard.controls.takedown">Takedown</span>@endif</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-yt-text-secondary" data-i18n="admin.dashboard.flagged.empty">No flagged pastes. 🎉</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="content-card p-6 space-y-4 xl:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-yt-text" data-i18n="admin.dashboard.newest.title">Newest Pastes</h2>
                <span class="text-xs text-yt-text-secondary" data-i18n="admin.dashboard.newest.subtitle">Last 6 submissions</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-yt-text-secondary">
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.identifier">Identifier</th>
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.owner">Owner</th>
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.views">Views</th>
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.limit">Limit</th>
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.created">Created</th>
                            <th class="py-2 pr-4" data-i18n="admin.dashboard.newest.th.moderation">Moderation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yt-border/60">
                        @foreach($recentPastes as $paste)
                        <tr class="align-top">
                            <td class="py-3 pr-4 font-medium text-yt-text">
                                <a href="{{ route('paste.show', $paste->identifier) }}" target="@_blank" class="text-link">{{ $paste->identifier }}</a>
                                @if($paste->is_removed)
                                    <span class="ml-2 text-xs text-yt-error" data-i18n="admin.dashboard.labels.removed">Removed</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-yt-text-secondary">
                                @if($paste->user)
                                    {{ $paste->user->username }}
                                @else
                                    <span data-i18n="common.user.anonymous">Anonymous</span>
                                @endif
                            </td>
                            <td class="py-3 pr-4 text-yt-text-secondary">{{ number_format($paste->views) }}</td>
                            <td class="py-3 pr-4 text-yt-text-secondary">{{ $paste->view_limit ?? '—' }}</td>
                            <td class="py-3 pr-4 text-yt-text-secondary">{{ $paste->created_at->diffForHumans() }}</td>
                            <td class="py-3 pr-4">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('admin.pastes.index', ['q' => $paste->identifier]) }}" class="btn-secondary px-3 py-1 text-xs" data-i18n="admin.dashboard.controls.manage">Manage</a>
                                    <form action="{{ route('admin.takedown', $paste) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        <input type="text" name="reason" value="{{ $paste->removed_reason }}" class="input-field px-2 py-1 text-xs w-32" data-i18n-attr="placeholder" data-i18n-placeholder="admin.dashboard.inputs.reason_placeholder" placeholder="Reason" />
                                        <button class="btn-secondary px-3 py-1 text-xs">@if($paste->is_removed)<span data-i18n="admin.dashboard.controls.update">Update</span>@else<span data-i18n="admin.dashboard.controls.takedown">Takedown</span>@endif</button>
                                    </form>
                                    <form action="{{ route('admin.viewlimit', $paste) }}" method="POST" class="flex gap-2 items-center">
                                        @csrf
                                        <input type="number" min="1" name="view_limit" value="{{ $paste->view_limit }}" class="input-field px-2 py-1 text-xs w-24" data-i18n-attr="placeholder" data-i18n-placeholder="admin.dashboard.inputs.limit_placeholder" placeholder="Limit" />
                                        <button class="btn-secondary px-3 py-1 text-xs" data-i18n="admin.dashboard.controls.apply">Apply</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="content-card p-6 space-y-5">
            <div>
                <h2 class="text-lg font-medium text-yt-text mb-3" data-i18n="admin.dashboard.side.top_pastes.title">Top Pastes by Views</h2>
                <ul class="space-y-3">
                    @forelse($topPastes as $paste)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-yt-text">{{ $paste->identifier }}</span>
                            <span class="text-yt-text-secondary">{{ number_format($paste->views) }} <span data-i18n="common.metrics.views">views</span></span>
                        </li>
                    @empty
                        <li class="text-sm text-yt-text-secondary" data-i18n="admin.dashboard.side.top_pastes.empty">No data available.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <h2 class="text-lg font-medium text-yt-text mb-3" data-i18n="admin.dashboard.side.latest_files.title">Latest Files</h2>
                <ul class="space-y-3 text-sm">
                    @forelse($recentFiles as $file)
                        <li class="flex items-center justify-between">
                            <span class="text-yt-text truncate max-w-[12rem]" title="{{ $file->original_filename }}">{{ $file->original_filename }}</span>
                            <span class="text-yt-text-secondary">{{ number_format($file->size_bytes / 1024, 1) }} <span data-i18n="common.units.kb">KB</span></span>
                        </li>
                    @empty
                        <li class="text-sm text-yt-text-secondary" data-i18n="admin.dashboard.side.latest_files.empty">No recent uploads.</li>
                    @endforelse
                </ul>
            </div>

            <div>
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-yt-text" data-i18n="admin.dashboard.editorial.title">Editorial</h2>
                    <a href="{{ route('admin.posts.index') }}" class="text-xs text-yt-accent" data-i18n="admin.dashboard.editorial.view_all">View all</a>
                </div>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse($recentPosts as $post)
                        <li class="flex items-center justify-between">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-link">{{ $post->title }}</a>
                            <span class="text-xs text-yt-text-secondary">@if($post->is_published)<span data-i18n="admin.dashboard.editorial.published">Published</span>@else<span data-i18n="admin.dashboard.editorial.draft">Draft</span>@endif</span>
                        </li>
                    @empty
                        <li class="text-sm text-yt-text-secondary" data-i18n="admin.dashboard.editorial.empty">No posts yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

