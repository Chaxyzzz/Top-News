<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Services\ArticleEngagementService;
use Illuminate\Contracts\View\View;

class EngagementOverviewController extends Controller
{
    public function __construct(
        protected ArticleEngagementService $engagementService
    ) {}

    /**
     * Display engagement overview dashboard.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Comment::class);

        $overview = $this->engagementService->getAdminOverview();

        return view('admin.engagement.index', compact('overview'));
    }
}
