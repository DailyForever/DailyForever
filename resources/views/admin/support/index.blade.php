@extends('layouts.app')

@section('title', 'Support Reports - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="content-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-yt-text" data-i18n="admin.support.index.title" data-i18n-doc-title="admin.support.index.doc_title">Support Reports</h1>
            <div class="flex gap-3">
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.support.index.back">← Back to Dashboard</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-yt-success/20 border border-yt-success rounded-lg p-4 mb-6">
                <p class="text-yt-success font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-text">{{ $stats['total_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.total">Total Reports</div>
            </div>
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-warning">{{ $stats['pending_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.pending">Pending</div>
            </div>
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-success">{{ $stats['resolved_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.resolved">Resolved</div>
            </div>
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-accent">{{ $stats['dmca_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.dmca">DMCA</div>
            </div>
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-error">{{ $stats['abuse_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.abuse">Abuse</div>
            </div>
            <div class="bg-yt-elevated border border-yt-border rounded-lg p-4">
                <div class="text-2xl font-bold text-yt-text">{{ $stats['security_reports'] }}</div>
                <div class="text-sm text-yt-text-secondary" data-i18n="admin.support.index.stats.security">Security</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-yt-elevated border border-yt-border rounded-lg p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label for="type" class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.index.filters.type">Type</label>
                    <select name="type" id="type" class="input-field px-3 py-2 text-sm">
                        <option value="" data-i18n="admin.support.index.filters.all_types">All Types</option>
                        <option value="dmca" {{ request('type') === 'dmca' ? 'selected' : '' }} data-i18n="admin.support.index.filters.dmca">DMCA</option>
                        <option value="abuse" {{ request('type') === 'abuse' ? 'selected' : '' }} data-i18n="admin.support.index.filters.abuse">Abuse</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }} data-i18n="admin.support.index.filters.general">General</option>
                        <option value="security" {{ request('type') === 'security' ? 'selected' : '' }} data-i18n="admin.support.index.filters.security">Security</option>
                        <option value="appeal" {{ request('type') === 'appeal' ? 'selected' : '' }} data-i18n="admin.support.index.filters.appeal">Appeal</option>
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.index.filters.status">Status</label>
                    <select name="status" id="status" class="input-field px-3 py-2 text-sm">
                        <option value="" data-i18n="admin.support.index.filters.all_statuses">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }} data-i18n="admin.support.index.filters.pending">Pending</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }} data-i18n="admin.support.index.filters.in_progress">In Progress</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }} data-i18n="admin.support.index.filters.resolved">Resolved</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }} data-i18n="admin.support.index.filters.closed">Closed</option>
                    </select>
                </div>
                <div>
                    <label for="search" class="block text-sm font-medium text-yt-text mb-1" data-i18n="admin.support.index.filters.search">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           class="input-field px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="admin.support.index.filters.search_placeholder" placeholder="Subject, email, or identifier...">
                </div>
                <button type="submit" class="btn-primary px-4 py-2 text-sm" data-i18n="admin.support.index.buttons.filter">Filter</button>
                <a href="{{ route('admin.support.index') }}" class="btn-secondary px-4 py-2 text-sm" data-i18n="admin.support.index.buttons.clear">Clear</a>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="bg-yt-elevated border border-yt-border rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-yt-surface border-b border-yt-border">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.id">ID</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.type">Type</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.subject">Subject</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.status">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.email">Email</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.created">Created</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-yt-text" data-i18n="admin.support.index.th.actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-yt-border">
                        @forelse($reports as $report)
                            <tr class="hover:bg-yt-bg/50">
                                <td class="px-4 py-3 text-sm text-yt-text font-mono">#{{ $report->id }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $report->type === 'dmca' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $report->type === 'abuse' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $report->type === 'security' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $report->type === 'general' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $report->type === 'appeal' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ $report->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-yt-text">
                                    <div class="max-w-xs truncate" title="{{ $report->subject }}">
                                        {{ $report->subject }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $report->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $report->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $report->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $report->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-yt-text">
                                    @if($report->email)
                                        <a href="mailto:{{ $report->email }}" class="text-link">{{ $report->email }}</a>
                                    @else
                                        <span class="text-yt-text-disabled">No email</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-yt-text-secondary">
                                    {{ $report->created_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.support.show', $report) }}"
                                       class="btn-secondary px-3 py-1 text-xs" data-i18n="common.buttons.view">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-yt-text-secondary" data-i18n="admin.support.index.empty">
                                    No support reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($reports->hasPages())
            <div class="mt-6">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
