<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paste;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Get date range (last 30 days by default)
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays(30);

        // Overall Statistics
        $stats = [
            'total_pastes' => Paste::count(),
            'total_files' => File::count(),
            'total_users' => User::count(),
            'total_views' => Paste::sum('views') + File::sum('views'),
            'total_storage' => File::sum('size_bytes'),
            'pastes_this_month' => Paste::where('created_at', '>=', $startDate)->count(),
            'files_this_month' => File::where('created_at', '>=', $startDate)->count(),
            'users_this_month' => User::where('created_at', '>=', $startDate)->count(),
        ];

        // Daily activity for the last 30 days
        $dailyActivity = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $endDate->copy()->subDays($i);
            $dailyActivity[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M j'),
                'pastes' => Paste::whereDate('created_at', $date)->count(),
                'files' => File::whereDate('created_at', $date)->count(),
                'users' => User::whereDate('created_at', $date)->count(),
                'views' => Paste::whereDate('created_at', $date)->sum('views') + 
                          File::whereDate('created_at', $date)->sum('views'),
            ];
        }

        // Top pastes by views
        $topPastes = Paste::with('user')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        // Top files by downloads
        $topFiles = File::with('user')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Storage usage by file type
        $storageByType = File::select('original_filename')
            ->get()
            ->groupBy(function ($file) {
                $extension = pathinfo($file->original_filename, PATHINFO_EXTENSION);
                return $extension ? strtoupper($extension) : 'NO EXTENSION';
            })
            ->map(function ($files) {
                return [
                    'count' => $files->count(),
                    'size' => $files->sum('size_bytes'),
                    'size_mb' => round($files->sum('size_bytes') / (1024 * 1024), 2)
                ];
            })
            ->sortByDesc('size')
            ->take(10);

        // Hourly activity (last 24 hours)
        $hourlyActivity = [];
        for ($i = 23; $i >= 0; $i--) {
            $hour = Carbon::now()->subHours($i);
            $hourlyActivity[] = [
                'hour' => $hour->format('H:i'),
                'pastes' => Paste::whereBetween('created_at', [
                    $hour->copy()->startOfHour(),
                    $hour->copy()->endOfHour()
                ])->count(),
                'files' => File::whereBetween('created_at', [
                    $hour->copy()->startOfHour(),
                    $hour->copy()->endOfHour()
                ])->count(),
            ];
        }

        // User activity stats
        $usersWithPastes = User::whereHas('pastes')->count();
        $usersWithFiles = User::whereHas('files')->count();
        
        // Calculate averages manually to avoid withCount issues
        $averagePastesPerUser = 0;
        if ($usersWithPastes > 0) {
            $totalPastes = Paste::whereNotNull('user_id')->count();
            $averagePastesPerUser = $totalPastes / $usersWithPastes;
        }
        
        $averageFilesPerUser = 0;
        if ($usersWithFiles > 0) {
            $totalFiles = File::whereNotNull('user_id')->count();
            $averageFilesPerUser = $totalFiles / $usersWithFiles;
        }
        
        $userStats = [
            'active_users' => User::whereHas('pastes')->orWhereHas('files')->count(),
            'users_with_pastes' => $usersWithPastes,
            'users_with_files' => $usersWithFiles,
            'average_pastes_per_user' => $averagePastesPerUser,
            'average_files_per_user' => $averageFilesPerUser,
        ];

        // Content statistics
        $contentStats = [
            'private_pastes' => Paste::where('is_private', true)->count(),
            'public_pastes' => Paste::where('is_private', false)->count(),
            'password_protected_pastes' => Paste::whereNotNull('password_hash')->count(),
            'expired_pastes' => Paste::where('expires_at', '<', now())->count(),
            'pastes_with_view_limits' => Paste::whereNotNull('view_limit')->count(),
        ];

        return view('admin.analytics', compact(
            'stats',
            'dailyActivity',
            'topPastes',
            'topFiles',
            'recentUsers',
            'storageByType',
            'hourlyActivity',
            'userStats',
            'contentStats',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'overview');

        // This would implement data export functionality
        // For now, just return a placeholder response
        return response()->json(['message' => 'Export functionality coming soon']);
    }
}
