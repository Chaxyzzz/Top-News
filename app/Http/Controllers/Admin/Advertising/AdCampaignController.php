<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Enums\AdCampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Models\AdCampaign;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdCampaignController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', AdCampaign::class);

        $query = AdCampaign::withCount('advertisements')->with('createdBy');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('advertiser', 'like', "%{$search}%");
            });
        }

        $campaigns = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.advertising.campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $this->authorize('create', AdCampaign::class);

        $statuses = AdCampaignStatus::cases();

        return view('admin.advertising.campaigns.create', compact('statuses'));
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $this->authorize('create', AdCampaign::class);

        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $campaign = AdCampaign::create($validated);
        $this->adService->clearCache();

        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', "Kampanye '{$campaign->name}' berhasil dibuat.");
    }

    public function edit(AdCampaign $campaign): View
    {
        $this->authorize('update', $campaign);

        $statuses = AdCampaignStatus::cases();

        return view('admin.advertising.campaigns.edit', compact('campaign', 'statuses'));
    }

    public function update(UpdateCampaignRequest $request, AdCampaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        $campaign->update($request->validated());
        $this->adService->clearCache();

        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', "Kampanye '{$campaign->name}' berhasil diperbarui.");
    }

    public function toggleStatus(AdCampaign $campaign): RedirectResponse
    {
        $this->authorize('update', $campaign);

        if ($campaign->status === AdCampaignStatus::Active) {
            $campaign->status = AdCampaignStatus::Paused;
        } else {
            $campaign->status = AdCampaignStatus::Active;
        }

        $campaign->save();
        $this->adService->clearCache();

        return back()->with('success', "Status kampanye '{$campaign->name}' diubah menjadi {$campaign->status->label()}.");
    }

    public function destroy(AdCampaign $campaign): RedirectResponse
    {
        $this->authorize('delete', $campaign);

        $name = $campaign->name;
        DB::transaction(function () use ($campaign) {
            foreach ($campaign->advertisements as $ad) {
                $ad->forceDelete();
            }
            $campaign->forceDelete();
        });
        $this->adService->clearCache();

        return redirect()->route('admin.advertising.campaigns.index')
            ->with('success', "Kampanye '{$name}' berhasil dihapus permanen.");
    }
}
