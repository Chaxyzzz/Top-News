<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Resolve author scope based on user role and permissions.
     */
    protected function resolveAuthorScope(): ?int
    {
        $user = Auth::user();

        // Super Admin, Admin, and Chief Editors have portal-wide scope
        if ($user->isSuperAdmin() || $user->hasRole('admin') || $user->hasRole('editor_in_chief') || $user->hasRole('managing_editor')) {
            return null;
        }

        // Journalists and contributors see only their own content metrics
        if ($user->hasRole('journalist') || $user->hasRole('contributor')) {
            return $user->id;
        }

        return null;
    }

    /**
     * Display Analytics Overview Dashboard.
     */
    public function overview(Request $request): View
    {
        $user = Auth::user();
        if (! $user->hasPermission('analytics.view')) {
            abort(403, 'Akses ke dashboard analitik ditolak.');
        }

        $preset = (string) $request->input('period', '7days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $range = $this->analyticsService->resolveDateRange($preset, $startDate, $endDate);
        $authorScope = $this->resolveAuthorScope();

        $metrics = $this->analyticsService->getOverviewMetrics($range['start'], $range['end'], $authorScope);

        return view('admin.analytics.overview', [
            'metrics' => $metrics,
            'range' => $range,
            'preset' => $range['preset'],
            'isScoped' => $authorScope !== null,
        ]);
    }

    /**
     * Display Detailed Content Analytics.
     */
    public function content(Request $request): View
    {
        $user = Auth::user();
        if (! $user->hasPermission('analytics.content')) {
            abort(403, 'Akses ke analitik konten ditolak.');
        }

        $preset = (string) $request->input('period', '7days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sort = (string) $request->input('sort', 'views');

        $range = $this->analyticsService->resolveDateRange($preset, $startDate, $endDate);
        $authorScope = $this->resolveAuthorScope();

        $articles = $this->analyticsService->getContentMetrics(
            $range['start'],
            $range['end'],
            $authorScope,
            20,
            $sort
        );

        $categoryBreakdown = $this->analyticsService->getCategoryBreakdown($range['start'], $range['end']);
        $authorRankings = $authorScope === null
            ? $this->analyticsService->getAuthorPerformance($range['start'], $range['end'])
            : collect();

        return view('admin.analytics.content', [
            'articles' => $articles,
            'categoryBreakdown' => $categoryBreakdown,
            'authorRankings' => $authorRankings,
            'range' => $range,
            'preset' => $range['preset'],
            'sort' => $sort,
            'isScoped' => $authorScope !== null,
        ]);
    }

    /**
     * Display Traffic Source & Device Breakdown.
     */
    public function traffic(Request $request): View
    {
        $user = Auth::user();
        if (! $user->hasPermission('analytics.traffic')) {
            abort(403, 'Akses ke analitik sumber trafik dibatasi untuk dewan redaksi dan manajemen.');
        }

        $preset = (string) $request->input('period', '7days');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $range = $this->analyticsService->resolveDateRange($preset, $startDate, $endDate);

        $traffic = $this->analyticsService->getTrafficSources($range['start'], $range['end']);
        $devices = $this->analyticsService->getDeviceBreakdown($range['start'], $range['end']);

        return view('admin.analytics.traffic', [
            'traffic' => $traffic,
            'devices' => $devices,
            'range' => $range,
            'preset' => $range['preset'],
        ]);
    }
}
