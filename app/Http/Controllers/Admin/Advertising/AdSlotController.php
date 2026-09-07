<?php

namespace App\Http\Controllers\Admin\Advertising;

use App\Http\Controllers\Controller;
use App\Models\AdSlot;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdSlotController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    public function index(): View
    {
        $this->authorize('viewAny', AdSlot::class);

        $slots = AdSlot::withCount('advertisements')->get();

        return view('admin.advertising.slots.index', compact('slots'));
    }

    public function toggleActive(AdSlot $slot): RedirectResponse
    {
        $this->authorize('update', $slot);

        $slot->is_active = ! $slot->is_active;
        $slot->save();
        $this->adService->clearCache($slot->key);

        $statusText = $slot->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Slot iklan '{$slot->name}' berhasil {$statusText}.");
    }
}
