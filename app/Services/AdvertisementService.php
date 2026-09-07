<?php

namespace App\Services;

use App\Models\AdCampaign;
use App\Models\AdDailyStat;
use App\Models\AdSlot;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdvertisementService
{
    public const CACHE_PREFIX = 'topnews.ad.slot.v2.';

    /**
     * Resolve single highest priority eligible ad for a slot.
     */
    public function getEligibleAdForSlot(string $slotKey, ?string $device = null): ?Advertisement
    {
        $cacheKey = self::CACHE_PREFIX.$slotKey.($device ? '.'.$device : '');

        try {
            $cached = Cache::get($cacheKey);
            if ($cached instanceof \__PHP_Incomplete_Class) {
                Cache::forget($cacheKey);
                $cached = null;
            }
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if ($cached instanceof Advertisement) {
            return $cached;
        }

        Cache::forget($cacheKey);

        return Cache::remember($cacheKey, 60, function () use ($slotKey, $device) {
            $query = Advertisement::currentlyEligible()
                ->whereHas('slot', function ($q) use ($slotKey, $device) {
                    $q->where('key', $slotKey);
                    if ($device && $device !== 'all') {
                        $q->whereIn('device_scope', ['all', $device]);
                    }
                })
                ->with(['media', 'slot', 'campaign']);

            return $query->first();
        });
    }

    /**
     * Record an ad click atomically and return safe destination URL.
     */
    public function recordClick(Advertisement $ad): string
    {
        // 1. Atomic lifetime counter increment
        $ad->increment('clicks_count');

        // 2. Atomic daily counter increment
        $today = now()->toDateString();
        AdDailyStat::updateOrInsert(
            ['advertisement_id' => $ad->id, 'date' => $today],
            ['updated_at' => now()]
        );
        DB::table('ad_daily_stats')
            ->where('advertisement_id', $ad->id)
            ->where('date', $today)
            ->increment('clicks');

        // 3. Return pre-validated stored destination URL
        return $ad->destination_url;
    }

    /**
     * Record an ad impression atomically.
     */
    public function recordImpression(Advertisement $ad): void
    {
        // 1. Atomic lifetime counter increment
        $ad->increment('impressions_count');

        // 2. Atomic daily counter increment
        $today = now()->toDateString();
        AdDailyStat::updateOrInsert(
            ['advertisement_id' => $ad->id, 'date' => $today],
            ['updated_at' => now()]
        );
        DB::table('ad_daily_stats')
            ->where('advertisement_id', $ad->id)
            ->where('date', $today)
            ->increment('impressions');
    }

    /**
     * Get aggregate overview statistics for admin advertising dashboard.
     *
     * @return array<string, mixed>
     */
    public function getOverviewStats(): array
    {
        $totalCampaigns = AdCampaign::count();
        $activeCampaigns = AdCampaign::active()->count();
        $totalAds = Advertisement::count();
        $activeAds = Advertisement::currentlyEligible()->count();
        $totalSlots = AdSlot::count();

        $totals = Advertisement::selectRaw('SUM(impressions_count) as total_impressions, SUM(clicks_count) as total_clicks')->first();
        $totalImpressions = (int) ($totals->total_impressions ?? 0);
        $totalClicks = (int) ($totals->total_clicks ?? 0);
        $averageCtr = $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0.0;

        return [
            'total_campaigns' => $totalCampaigns,
            'active_campaigns' => $activeCampaigns,
            'total_ads' => $totalAds,
            'active_ads' => $activeAds,
            'total_slots' => $totalSlots,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'average_ctr' => $averageCtr,
        ];
    }

    /**
     * Clear ad resolution caches.
     */
    public function clearCache(?string $slotKey = null): void
    {
        $prefixes = [self::CACHE_PREFIX, 'topnews.ad.slot.'];

        if ($slotKey) {
            foreach ($prefixes as $p) {
                Cache::forget($p.$slotKey);
                Cache::forget($p.$slotKey.'.desktop');
                Cache::forget($p.$slotKey.'.mobile');
            }
        } else {
            $slots = AdSlot::pluck('key')->all();
            foreach ($slots as $key) {
                foreach ($prefixes as $p) {
                    Cache::forget($p.$key);
                    Cache::forget($p.$key.'.desktop');
                    Cache::forget($p.$key.'.mobile');
                }
            }
        }
    }
}
