@extends('layouts.app')

@section('title', 'Analytics - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-yt-text" data-i18n="admin.analytics.title" data-i18n-doc-title="admin.analytics.doc_title">Analytics Dashboard</h1>
                <p class="text-yt-text-secondary mt-1" data-i18n="admin.analytics.subtitle">Website performance and user activity insights</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.data_range">Data Range</div>
                    <div class="text-sm font-medium text-yt-text">{{ $startDate->format('M j') }} - {{ $endDate->format('M j, Y') }}</div>
                </div>
                <button class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span data-i18n="admin.analytics.buttons.export">Export Data</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="content-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="admin.analytics.cards.total_pastes">Total Pastes</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_pastes']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $stats['pastes_this_month'] }} <span data-i18n="admin.analytics.cards.this_month">this month</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="admin.analytics.cards.total_files">Total Files</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_files']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $stats['files_this_month'] }} <span data-i18n="admin.analytics.cards.this_month">this month</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="admin.analytics.cards.total_users">Total Users</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ $stats['users_this_month'] }} <span data-i18n="admin.analytics.cards.this_month">this month</span></p>
                </div>
            </div>
        </div>

        <div class="content-card p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yt-text-secondary" data-i18n="admin.analytics.cards.total_views">Total Views</p>
                    <p class="text-2xl font-semibold text-yt-text">{{ number_format($stats['total_views']) }}</p>
                    <p class="text-xs text-yt-text-secondary">{{ number_format($stats['total_storage'] / (1024*1024*1024), 2) }} <span data-i18n="common.units.gb">GB</span> <span data-i18n="admin.analytics.cards.stored">stored</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Daily Activity Chart -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.charts.daily">Daily Activity (Last 30 Days)</h3>
            <div class="h-64">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>

        <!-- Hourly Activity Chart -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.charts.hourly">Hourly Activity (Last 24 Hours)</h3>
            <div class="h-64">
                <canvas id="hourlyActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Content Stats -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.sections.content.title">Content Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.content.private_pastes">Private Pastes</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($contentStats['private_pastes']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.content.public_pastes">Public Pastes</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($contentStats['public_pastes']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.content.password_protected">Password Protected</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($contentStats['password_protected_pastes']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.content.expired_pastes">Expired Pastes</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($contentStats['expired_pastes']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.content.with_view_limits">With View Limits</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($contentStats['pastes_with_view_limits']) }}</span>
                </div>
            </div>
        </div>

        <!-- User Stats -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.sections.user.title">User Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.user.active_users">Active Users</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($userStats['active_users']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.user.users_with_pastes">Users with Pastes</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($userStats['users_with_pastes']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.user.users_with_files">Users with Files</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($userStats['users_with_files']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.user.avg_pastes">Avg Pastes/User</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($userStats['average_pastes_per_user'], 1) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-yt-text-secondary" data-i18n="admin.analytics.sections.user.avg_files">Avg Files/User</span>
                    <span class="text-sm font-medium text-yt-text">{{ number_format($userStats['average_files_per_user'], 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Storage by Type -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.sections.storage.title">Storage by File Type</h3>
            <div class="space-y-3">
                @foreach($storageByType as $type => $data)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-yt-text-secondary">{{ $type }}</span>
                        <div class="text-right">
                            <div class="text-sm font-medium text-yt-text">{{ number_format($data['count']) }} <span data-i18n="common.metrics.files">files</span></div>
                            <div class="text-xs text-yt-text-secondary">{{ $data['size_mb'] }} <span data-i18n="common.units.mb">MB</span></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Top Content Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Pastes -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.top.pastes">Top Pastes by Views</h3>
            <div class="space-y-3">
                @forelse($topPastes as $paste)
                    <div class="flex items-center justify-between p-3 bg-yt-surface rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yt-text">{{ $paste->identifier }}</p>
                                <p class="text-xs text-yt-text-secondary">
                                    @if($paste->user)
                                        {{ $paste->user->username }}
                                    @else
                                        <span data-i18n="common.user.anonymous">Anonymous</span>
                                    @endif
                                    • {{ $paste->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-yt-text">{{ number_format($paste->views) }}</span>
                            <div class="text-xs text-yt-text-secondary" data-i18n="common.metrics.views">views</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-yt-text-secondary text-sm" data-i18n="admin.analytics.empty.pastes">No pastes found</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Files -->
        <div class="content-card p-6">
            <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.top.files">Top Files by Downloads</h3>
            <div class="space-y-3">
                @forelse($topFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-yt-surface rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yt-text truncate max-w-32" title="{{ $file->original_filename }}">{{ $file->original_filename }}</p>
                                <p class="text-xs text-yt-text-secondary">
                                    @if($file->user)
                                        {{ $file->user->username }}
                                    @else
                                        <span data-i18n="common.user.anonymous">Anonymous</span>
                                    @endif
                                    • {{ $file->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-yt-text">{{ number_format($file->views) }}</span>
                            <div class="text-xs text-yt-text-secondary" data-i18n="common.metrics.downloads">downloads</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-yt-text-secondary text-sm" data-i18n="admin.analytics.empty.files">No files found</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="content-card p-6">
        <h3 class="text-lg font-semibold text-yt-text mb-4" data-i18n="admin.analytics.recent_users">Recent Users</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($recentUsers as $user)
                <div class="p-4 bg-yt-surface rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-yt-text">{{ $user->username }}</p>
                            <p class="text-xs text-yt-text-secondary">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-6">
                    <p class="text-yt-text-secondary text-sm" data-i18n="admin.analytics.empty.users">No users found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Activity Chart
    const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($dailyActivity)->pluck('label')) !!},
            datasets: [
                {
                    label: 'Pastes',
                    data: {!! json_encode(collect($dailyActivity)->pluck('pastes')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Files',
                    data: {!! json_encode(collect($dailyActivity)->pluck('files')) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Users',
                    data: {!! json_encode(collect($dailyActivity)->pluck('users')) !!},
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text')
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-border')
                    }
                },
                y: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-border')
                    }
                }
            }
        }
    });

    // Hourly Activity Chart
    const hourlyCtx = document.getElementById('hourlyActivityChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($hourlyActivity)->pluck('hour')) !!},
            datasets: [
                {
                    label: 'Pastes',
                    data: {!! json_encode(collect($hourlyActivity)->pluck('pastes')) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.8)'
                },
                {
                    label: 'Files',
                    data: {!! json_encode(collect($hourlyActivity)->pluck('files')) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.8)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text')
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-border')
                    }
                },
                y: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-text-secondary')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--color-border')
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
