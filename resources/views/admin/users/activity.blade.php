@extends('layouts.app')

@section('title', 'User Activity - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-yt-text">{{ $user->username }} - Activity</h1>
                <p class="text-yt-text-secondary">Recent activity and content history</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.users.show', $user) }}" class="btn-secondary">Back to User</a>
                <a href="{{ route('admin.users.index') }}" class="btn-primary">All Users</a>
            </div>
        </div>
        
        <!-- Privacy Notice -->
        <div class="bg-yt-accent/10 border border-yt-accent rounded-lg p-4 mt-4">
            <h3 class="font-semibold text-yt-accent mb-2">Privacy Notice</h3>
            <p class="text-sm text-yt-text">
                Activity data shows only metadata (creation times, view counts, file sizes). We cannot access encrypted content due to our zero-knowledge architecture.
            </p>
        </div>
    </div>

    <!-- Activity Timeline -->
    <div class="content-card p-6">
        <h3 class="text-lg font-medium text-yt-text mb-6">Activity Timeline</h3>
        
        @if($activities->count() > 0)
            <div class="space-y-4">
                @foreach($activities as $activity)
                <div class="flex items-start space-x-4 p-4 bg-yt-hover rounded-lg">
                    <div class="flex-shrink-0">
                        @if($activity['type'] === 'paste')
                            <div class="w-8 h-8 bg-yt-accent rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-8 h-8 bg-yt-success rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div>
                                @if($activity['type'] === 'paste')
                                    <h4 class="text-sm font-medium text-yt-text">
                                        {{ $activity['action'] === 'created' ? 'Created paste' : 'Removed paste' }}
                                        <span class="font-mono text-yt-accent">{{ $activity['identifier'] }}</span>
                                    </h4>
                                    <p class="text-sm text-yt-text-secondary">
                                        {{ $activity['views'] }} views
                                        @if($activity['action'] === 'removed' && $activity['removed_at'])
                                            • Removed {{ \Carbon\Carbon::parse($activity['removed_at'])->diffForHumans() }}
                                        @endif
                                    </p>
                                @else
                                    <h4 class="text-sm font-medium text-yt-text">
                                        Uploaded file
                                        <span class="text-yt-text-secondary">{{ $activity['filename'] }}</span>
                                    </h4>
                                    <p class="text-sm text-yt-text-secondary">
                                        {{ number_format($activity['size'] / 1024, 1) }} KB • {{ $activity['views'] }} views
                                    </p>
                                @endif
                            </div>
                            <div class="text-sm text-yt-text-secondary">
                                {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}
                            </div>
                        </div>
                        
                        <div class="mt-2 flex items-center space-x-2">
                            @if($activity['type'] === 'paste')
                                <a href="{{ route('paste.show', $activity['identifier']) }}" 
                                   class="btn-secondary px-2 py-1 text-xs">View Paste</a>
                                @if($activity['action'] === 'removed')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-warning text-white">
                                        Removed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yt-success text-white">
                                        Active
                                    </span>
                                @endif
                            @else
                                <a href="{{ route('files.show', $activity['identifier']) }}" 
                                   class="btn-secondary px-2 py-1 text-xs">View File</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-yt-text-secondary mb-2">No activity found</div>
                <p class="text-sm text-yt-text-disabled">This user hasn't created any pastes or uploaded any files yet.</p>
            </div>
        @endif
    </div>

    <!-- Activity Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Paste Activity</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Pastes</span>
                    <span class="text-yt-text font-medium">{{ $activities->where('type', 'paste')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Active Pastes</span>
                    <span class="text-yt-text font-medium">{{ $activities->where('type', 'paste')->where('action', 'created')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Removed Pastes</span>
                    <span class="text-yt-text font-medium">{{ $activities->where('type', 'paste')->where('action', 'removed')->count() }}</span>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">File Activity</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Files</span>
                    <span class="text-yt-text font-medium">{{ $activities->where('type', 'file')->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Size</span>
                    <span class="text-yt-text font-medium">
                        {{ number_format($activities->where('type', 'file')->sum('size') / 1024 / 1024, 2) }} MB
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-yt-text-secondary">Total Views</span>
                    <span class="text-yt-text font-medium">{{ number_format($activities->sum('views')) }}</span>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <h3 class="text-lg font-medium text-yt-text mb-4">Recent Activity</h3>
            <div class="space-y-2">
                @if($activities->count() > 0)
                    <div class="text-sm text-yt-text-secondary">
                        Last activity: {{ \Carbon\Carbon::parse($activities->first()['created_at'])->diffForHumans() }}
                    </div>
                    <div class="text-sm text-yt-text-secondary">
                        Most active type: {{ $activities->groupBy('type')->map->count()->sortDesc()->keys()->first() ?? 'None' }}
                    </div>
                    <div class="text-sm text-yt-text-secondary">
                        Total activities: {{ $activities->count() }}
                    </div>
                @else
                    <div class="text-sm text-yt-text-secondary">No activity data available</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
