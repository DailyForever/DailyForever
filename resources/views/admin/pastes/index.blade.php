@extends('layouts.app')

@section('title', 'Manage Pastes - DailyForever Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-yt-text" data-i18n="admin.pastes.index.title" data-i18n-doc-title="admin.pastes.index.doc_title">Manage Pastes</h1>
            <p class="text-yt-text-secondary" data-i18n="admin.pastes.index.subtitle">Search, filter, and moderate submitted pastes.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="content-card px-4 py-2 text-center">
                <div class="text-xs text-yt-text-secondary uppercase tracking-wide" data-i18n="admin.pastes.index.stats.active">Active</div>
                <div class="text-lg font-semibold text-yt-text">{{ number_format($counts['active']) }}</div>
            </div>
            <div class="content-card px-4 py-2 text-center">
                <div class="text-xs text-yt-text-secondary uppercase tracking-wide" data-i18n="admin.pastes.index.stats.removed">Removed</div>
                <div class="text-lg font-semibold text-yt-text">{{ number_format($counts['removed']) }}</div>
            </div>
            <div class="content-card px-4 py-2 text-center">
                <div class="text-xs text-yt-text-secondary uppercase tracking-wide" data-i18n="admin.pastes.index.stats.private">Private</div>
                <div class="text-lg font-semibold text-yt-text">{{ number_format($counts['private']) }}</div>
            </div>
            <div class="content-card px-4 py-2 text-center">
                <div class="text-xs text-yt-text-secondary uppercase tracking-wide" data-i18n="admin.pastes.index.stats.public">Public</div>
                <div class="text-lg font-semibold text-yt-text">{{ number_format($counts['public']) }}</div>
            </div>
        </div>
    </div>

    <form method="GET" class="content-card p-5 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-xs uppercase tracking-wide text-yt-text-secondary mb-1" data-i18n="admin.pastes.index.filters.search">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="admin.pastes.index.filters.search_placeholder" placeholder="Identifier or username" />
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wide text-yt-text-secondary mb-1" data-i18n="admin.pastes.index.filters.status">Status</label>
            <select name="status" class="input-field w-full px-3 py-2 text-sm">
                <option value="" data-i18n="admin.pastes.index.filters.any">Any</option>
                <option value="active" @selected(request('status')==='active') data-i18n="admin.pastes.index.filters.active">Active</option>
                <option value="removed" @selected(request('status')==='removed') data-i18n="admin.pastes.index.filters.removed">Removed</option>
            </select>
        </div>
        <div>
            <label class="block text-xs uppercase tracking-wide text-yt-text-secondary mb-1" data-i18n="admin.pastes.index.filters.privacy">Privacy</label>
            <select name="privacy" class="input-field w-full px-3 py-2 text-sm">
                <option value="" data-i18n="admin.pastes.index.filters.any">Any</option>
                <option value="public" @selected(request('privacy')==='public') data-i18n="admin.pastes.index.filters.public">Public</option>
                <option value="private" @selected(request('privacy')==='private') data-i18n="admin.pastes.index.filters.private">Private</option>
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2 justify-end">
            <a href="{{ route('admin.pastes.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.pastes.index.buttons.reset">Reset</a>
            <button class="btn-primary px-4 py-2 text-sm" data-i18n="admin.pastes.index.buttons.apply_filters">Apply Filters</button>
        </div>
    </form>

    <div class="content-card overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-yt-text-secondary">
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.identifier">Identifier</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.owner">Owner</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.status">Status</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.privacy">Privacy</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.views_limit">Views / Limit</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.created">Created</th>
                    <th class="px-4 py-3" data-i18n="admin.pastes.index.th.actions">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-yt-border/60">
                @forelse($pastes as $paste)
                    <tr class="align-top">
                        <td class="px-4 py-4 font-medium text-yt-text">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('paste.show', $paste->identifier) }}" target="_blank" class="text-link">{{ $paste->identifier }}</a>
                                <button type="button" class="btn-secondary px-2 py-1 text-xs" onclick="navigator.clipboard.writeText('{{ $paste->identifier }}')"><span data-i18n="common.buttons.copy">Copy</span></button>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-yt-text-secondary">
                            @if($paste->user)
                                {{ $paste->user->username }}
                            @else
                                <span data-i18n="common.user.anonymous">Anonymous</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            @if($paste->is_removed)
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yt-error/10 text-yt-error" data-i18n="admin.pastes.index.status.removed">Removed</span>
                                @if($paste->removed_reason)
                                    <div class="text-xs text-yt-text-secondary mt-1">{{ $paste->removed_reason }}</div>
                                @else
                                    <div class="text-xs text-yt-text-secondary mt-1" data-i18n="admin.pastes.index.reason_none">No reason provided</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-yt-success/10 text-yt-success" data-i18n="admin.pastes.index.status.active">Active</span>
                            @endif
                        </td>
                            <td class="px-4 py-4 text-yt-text-secondary">
                                @if($paste->is_private)
                                    <span data-i18n="admin.pastes.index.privacy.private">Private</span>
                                @else
                                    <span data-i18n="admin.pastes.index.privacy.public">Public</span>
                                @endif
                            </td>
                        <td class="px-4 py-4 text-yt-text-secondary">{{ number_format($paste->views) }} / {{ $paste->view_limit ?? '—' }}</td>
                        <td class="px-4 py-4 text-yt-text-secondary">
                            {{ $paste->created_at->format('Y-m-d H:i') }}
                            <div class="text-xs">{{ $paste->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <form action="{{ route('admin.takedown', $paste) }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="text" name="reason" value="{{ $paste->removed_reason }}" class="input-field px-2 py-1 text-xs w-36" data-i18n-attr="placeholder" data-i18n-placeholder="admin.dashboard.inputs.reason_placeholder" placeholder="Reason" />
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
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-yt-text-secondary" data-i18n="admin.pastes.index.empty">No pastes match your filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $pastes->links() }}
    </div>
</div>
@endsection

