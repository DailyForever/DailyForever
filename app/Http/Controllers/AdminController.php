<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paste;
use App\Models\Post;
use App\Models\File as StoredFile;
use App\Models\User;
use App\Models\SupportReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_pastes' => Paste::count(),
            'active_pastes' => Paste::where('is_removed', false)->count(),
            'removed_pastes' => Paste::where('is_removed', true)->count(),
            'pastes_today' => Paste::where('created_at', '>=', Carbon::now()->startOfDay())->count(),
            'total_files' => StoredFile::count(),
            'storage_used' => StoredFile::sum('size_bytes'),
            'total_users' => User::count(),
            'two_factor_enabled' => User::where('two_factor_enabled', true)->count(),
        ];

        $activity = Paste::selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(function ($row) {
                return [
                    'label' => Carbon::parse($row->day)->format('M j'),
                    'total' => (int) $row->total,
                ];
            });

        $activityMax = max(1, $activity->max('total')); // avoid division by zero

        $recentPastes = Paste::with('user')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $flaggedPastes = Paste::with('user')
            ->where('is_removed', true)
            ->orderByDesc('removed_at')
            ->limit(6)
            ->get();

        $topPastes = Paste::orderByDesc('views')
            ->limit(5)
            ->get();

        $recentPosts = Post::orderByDesc('id')->limit(4)->get();

        $recentFiles = StoredFile::orderByDesc('id')->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'activity',
            'activityMax',
            'recentPastes',
            'flaggedPastes',
            'topPastes',
            'recentPosts',
            'recentFiles'
        ));
    }

    public function pastes(Request $request)
    {
        $query = Paste::with('user')->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('identifier', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('username', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_removed', false);
            } elseif ($request->status === 'removed') {
                $query->where('is_removed', true);
            }
        }

        if ($request->filled('privacy')) {
            if ($request->privacy === 'private') {
                $query->where('is_private', true);
            } elseif ($request->privacy === 'public') {
                $query->where('is_private', false);
            }
        }

        $pastes = $query->paginate(25)->withQueryString();
        $counts = [
            'active' => Paste::where('is_removed', false)->count(),
            'removed' => Paste::where('is_removed', true)->count(),
            'private' => Paste::where('is_private', true)->count(),
            'public' => Paste::where('is_private', false)->count(),
        ];

        return view('admin.pastes.index', compact('pastes', 'counts'));
    }

    public function files()
    {
        $files = StoredFile::orderByDesc('id')->paginate(50);
        return view('admin.files.index', compact('files'));
    }

    public function setFileViewLimit(Request $request, StoredFile $file)
    {
        $request->validate(['view_limit' => 'nullable|integer|min:1|max:1000000']);
        $file->view_limit = $request->input('view_limit');
        $file->save();
        return back();
    }

    public function deleteFile(StoredFile $file)
    {
        try { @unlink(storage_path('app/'.$file->storage_path)); } catch (\Throwable $e) {}
        $file->delete();
        return back();
    }

    public function takedown(Request $request, Paste $paste)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $paste->is_removed = true;
        $paste->removed_reason = $request->input('reason');
        $paste->removed_at = Carbon::now();
        $paste->removed_by = $request->user()->id;
        $paste->save();

        return back();
    }

    public function setViewLimit(Request $request, Paste $paste)
    {
        $request->validate([
            'view_limit' => 'nullable|integer|min:1|max:1000000',
        ]);
        $paste->view_limit = $request->input('view_limit');
        $paste->save();
        return back();
    }

    public function support()
    {
        $reports = SupportReport::with('resolver')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_reports' => SupportReport::count(),
            'pending_reports' => SupportReport::pending()->count(),
            'resolved_reports' => SupportReport::resolved()->count(),
            'dmca_reports' => SupportReport::byType('dmca')->count(),
            'abuse_reports' => SupportReport::byType('abuse')->count(),
            'security_reports' => SupportReport::byType('security')->count(),
        ];

        return view('admin.support.index', compact('reports', 'stats'));
    }

    public function supportShow(SupportReport $report)
    {
        return view('admin.support.show', compact('report'));
    }

    public function supportUpdate(Request $request, SupportReport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:5000',
        ]);

        $report->status = $request->status;
        $report->admin_notes = $request->admin_notes;
        
        if ($request->status === 'resolved' && !$report->resolved_at) {
            $report->resolved_at = now();
            $report->resolved_by = auth()->id();
        }

        $report->save();

        return back()->with('success', 'Support report updated successfully.');
    }

    // User Management Methods
    public function users(Request $request)
    {
        $query = User::withCount(['pastes', 'files'])->orderByDesc('created_at');

        // Search functionality
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by admin status
        if ($request->filled('admin')) {
            if ($request->admin === 'yes') {
                $query->where('is_admin', true);
            } elseif ($request->admin === 'no') {
                $query->where('is_admin', false);
            }
        }

        // Filter by 2FA status
        if ($request->filled('two_factor')) {
            if ($request->two_factor === 'enabled') {
                $query->where('two_factor_enabled', true);
            } elseif ($request->two_factor === 'disabled') {
                $query->where('two_factor_enabled', false);
            }
        }

        // Filter by registration date
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $users = $query->paginate(25)->withQueryString();

        // Statistics
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'regular_users' => User::where('is_admin', false)->count(),
            'two_factor_enabled' => User::where('two_factor_enabled', true)->count(),
            'users_today' => User::where('created_at', '>=', Carbon::now()->startOfDay())->count(),
            'users_this_week' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'users_this_month' => User::where('created_at', '>=', Carbon::now()->subMonth())->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function userShow(User $user)
    {
        $user->loadCount(['pastes', 'files']);
        
        // Recent activity
        $recentPastes = $user->pastes()->orderByDesc('created_at')->limit(10)->get();
        $recentFiles = $user->files()->orderByDesc('created_at')->limit(10)->get();
        
        // Statistics
        $stats = [
            'total_pastes' => $user->pastes()->count(),
            'active_pastes' => $user->pastes()->where('is_removed', false)->count(),
            'removed_pastes' => $user->pastes()->where('is_removed', true)->count(),
            'total_files' => $user->files()->count(),
            'total_views' => $user->pastes()->sum('views') + $user->files()->sum('views'),
            'storage_used' => $user->files()->sum('size_bytes'),
            'last_activity' => $user->pastes()->latest()->first()?->created_at ?? $user->created_at,
        ];

        return view('admin.users.show', compact('user', 'recentPastes', 'recentFiles', 'stats'));
    }

    public function toggleAdmin(User $user)
    {
        // Prevent admin from removing their own admin status
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot remove admin privileges from yourself.']);
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'granted' : 'revoked';
        
        

        return back()->with('success', "Admin privileges {$status} for {$user->username}.");
    }

    public function toggle2FA(User $user)
    {
        $user->two_factor_enabled = !$user->two_factor_enabled;
        $user->two_factor_confirmed_at = $user->two_factor_enabled ? now() : null;
        $user->save();

        $status = $user->two_factor_enabled ? 'enabled' : 'disabled';
        
        

        return back()->with('success', "Two-factor authentication {$status} for {$user->username}.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        

        return back()->with('success', "Password reset successfully for {$user->username}.");
    }

    public function suspendUser(Request $request, User $user)
    {
        // Prevent admin from suspending themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot suspend yourself.']);
        }

        $request->validate([
            'reason' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1|max:365', // days
        ]);

        $user->suspended_at = now();
        $user->suspended_until = $request->duration ? now()->addDays($request->duration) : null;
        $user->suspension_reason = $request->reason;
        $user->suspended_by = auth()->id();
        $user->save();

        

        $duration = $request->duration ? " for {$request->duration} days" : ' indefinitely';
        return back()->with('success', "User {$user->username} suspended{$duration}.");
    }

    public function deleteUser(Request $request, User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $request->validate([
            'confirmation' => 'required|in:DELETE',
        ]);

        $username = $user->username;
        
        // Delete user's pastes and files
        $user->pastes()->delete();
        $user->files()->delete();
        
        // Delete user
        $user->delete();

        

        return redirect()->route('admin.users.index')->with('success', "User {$username} deleted successfully.");
    }

    public function userActivity(User $user)
    {
        $activities = collect();

        // Get paste activities
        $pasteActivities = $user->pastes()
            ->select('id', 'identifier', 'created_at', 'views', 'is_removed', 'removed_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($paste) {
                return [
                    'type' => 'paste',
                    'action' => $paste->is_removed ? 'removed' : 'created',
                    'identifier' => $paste->identifier,
                    'views' => $paste->views,
                    'created_at' => $paste->created_at,
                    'removed_at' => $paste->removed_at,
                ];
            });

        // Get file activities
        $fileActivities = $user->files()
            ->select('id', 'identifier', 'original_filename', 'created_at', 'views', 'size_bytes')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($file) {
                return [
                    'type' => 'file',
                    'action' => 'uploaded',
                    'identifier' => $file->identifier,
                    'filename' => $file->original_filename,
                    'size' => $file->size_bytes,
                    'views' => $file->views,
                    'created_at' => $file->created_at,
                ];
            });

        // Combine and sort activities
        $activities = $activities
            ->merge($pasteActivities)
            ->merge($fileActivities)
            ->sortByDesc('created_at')
            ->take(50);

        return view('admin.users.activity', compact('user', 'activities'));
    }
}


