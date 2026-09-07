<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the Newsroom admin dashboard.
     */
    public function index(Request $request): View
    {
        $currentUser = Auth::user();

        // 1. Staff & Security Metrics
        $totalUsers = User::count();
        $activeUsers = User::where('status', UserStatus::Active)->count();
        $suspendedUsers = User::where('status', UserStatus::Suspended)->count();
        $totalRoles = Role::count();
        $auditToday = AuditLog::whereDate('created_at', today())->count();
        $recentLoginsCount = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        // 2. Real Editorial & Article Metrics (Phase 04)
        $editorialMetrics = [
            'total_articles' => Article::count(),
            'published_today' => Article::where('status', ArticleStatus::Published)->whereDate('published_at', today())->count(),
            'my_drafts' => Article::where('author_id', $currentUser->id)->where('status', ArticleStatus::Draft)->count(),
            'my_revisions' => Article::where('author_id', $currentUser->id)->where('status', ArticleStatus::RevisionRequested)->count(),
            'waiting_review' => Article::where('status', ArticleStatus::Submitted)->count(),
            'in_review' => Article::where('status', ArticleStatus::InReview)->count(),
            'approved' => Article::where('status', ArticleStatus::Approved)->count(),
            'scheduled' => Article::where('status', ArticleStatus::Scheduled)->count(),
        ];

        // 3. Recent activity feed
        $recentActivities = AuditLog::with('user')
            ->latest('created_at')
            ->limit(8)
            ->get();

        // 4. Role distribution
        $roleDistribution = Role::withCount('users')->get();

        // 5. Safe system status
        $databaseConnected = false;
        try {
            DB::connection()->getPdo();
            $databaseConnected = true;
        } catch (\Throwable) {
            $databaseConnected = false;
        }

        $systemStatus = [
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Aktif (Local)' : 'Nonaktif (Production)',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
            'db_driver' => config('database.default'),
            'db_connected' => $databaseConnected,
            'storage_linked' => file_exists(public_path('storage')),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'suspendedUsers',
            'totalRoles',
            'auditToday',
            'recentLoginsCount',
            'editorialMetrics',
            'recentActivities',
            'roleDistribution',
            'systemStatus'
        ));
    }
}
