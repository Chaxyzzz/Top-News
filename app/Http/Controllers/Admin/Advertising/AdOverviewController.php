<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\AdSlot;
use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Illuminate\View\View;

class AdOverviewController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', Advertisement::class);

        $stats = $this->adService->getOverviewStats();
        $recentAds = Advertisement::with(['campaign', 'slot', 'media'])
            ->latest('created_at')
            ->limit(8)
            ->get();
        $recentCampaigns = AdCampaign::withCount('advertisements')
            ->latest('created_at')
            ->limit(5)
            ->get();
        $slots = AdSlot::withCount('advertisements')->get();

        return view('admin.advertising.overview', compact('stats', 'recentAds', 'recentCampaigns', 'slots'));
    }
}
