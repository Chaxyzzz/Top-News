<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Enums\AdCampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdvertisementRequest;
use App\Http\Requests\UpdateAdvertisementRequest;
use App\Models\AdCampaign;
use App\Models\AdSlot;
use App\Models\Advertisement;
use App\Models\Media;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Advertisement::class);

        $query = Advertisement::with(['campaign', 'slot', 'media']);

        if ($request->filled('slot_id')) {
            $query->where('ad_slot_id', $request->input('slot_id'));
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->input('campaign_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $ads = $query->latest('created_at')->paginate(15)->withQueryString();
        $slots = AdSlot::all();
        $campaigns = AdCampaign::all();

        return view('admin.advertising.ads.index', compact('ads', 'slots', 'campaigns'));
    }

    public function create(): View
    {
        $this->authorize('create', Advertisement::class);

        $campaigns = AdCampaign::whereIn('status', [AdCampaignStatus::Active->value, AdCampaignStatus::Draft->value])->get();
        $slots = AdSlot::where('is_active', true)->get();
        $mediaItems = Media::images()->latest('created_at')->limit(30)->get();

        return view('admin.advertising.ads.create', compact('campaigns', 'slots', 'mediaItems'));
    }

    public function store(StoreAdvertisementRequest $request): RedirectResponse
    {
        $this->authorize('create', Advertisement::class);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;

        $ad = Advertisement::create($validated);
        $this->adService->clearCache($ad->slot?->key);

        return redirect()->route('admin.advertising.ads.index')
            ->with('success', "Materi iklan '{$ad->name}' berhasil disimpan.");
    }

    public function edit(Advertisement $advertisement): View
    {
        $this->authorize('update', $advertisement);

        $campaigns = AdCampaign::all();
        $slots = AdSlot::all();
        $mediaItems = Media::images()->latest('created_at')->limit(30)->get();

        return view('admin.advertising.ads.edit', [
            'ad' => $advertisement,
            'campaigns' => $campaigns,
            'slots' => $slots,
            'mediaItems' => $mediaItems,
        ]);
    }

    public function update(UpdateAdvertisementRequest $request, Advertisement $advertisement): RedirectResponse
    {
        $this->authorize('update', $advertisement);

        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;

        $advertisement->update($validated);
        $this->adService->clearCache($advertisement->slot?->key);

        return redirect()->route('admin.advertising.ads.index')
            ->with('success', "Materi iklan '{$advertisement->name}' berhasil diperbarui.");
    }

    public function toggleActive(Advertisement $advertisement): RedirectResponse
    {
        $this->authorize('update', $advertisement);

        $advertisement->is_active = ! $advertisement->is_active;
        $advertisement->save();
        $this->adService->clearCache($advertisement->slot?->key);

        $statusText = $advertisement->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Iklan '{$advertisement->name}' berhasil {$statusText}.");
    }

    public function destroy(Advertisement $advertisement): RedirectResponse
    {
        $this->authorize('delete', $advertisement);

        $name = $advertisement->name;
        $slotKey = $advertisement->slot?->key;
        $advertisement->forceDelete();

        $this->adService->clearCache($slotKey);

        return redirect()->route('admin.advertising.ads.index')
            ->with('success', "Iklan '{$name}' berhasil dihapus permanen.");
    }
}
