<?php

namespace App\Services;

use App\Models\BreakingNews;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class BreakingNewsService
{
    public const CACHE_KEY = 'topnews.breaking_news.v2.active';

    /**
     * Get active and eligible breaking news items for public display.
     *
     * @return Collection<int, BreakingNews>
     */
    public function getActiveBreakingNews(): Collection
    {
        $cached = null;
        try {
            $cached = Cache::get(self::CACHE_KEY);
        } catch (\Throwable) {
            Cache::forget(self::CACHE_KEY);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class) {
            Cache::forget(self::CACHE_KEY);
            $items = BreakingNews::currentlyActive()
                ->with(['article:id,title,slug,status,published_at,deleted_at'])
                ->get()
                ->filter(fn (BreakingNews $item) => $item->isEligibleForPublic())
                ->values();

            $cached = $items->pluck('id')->map(fn ($id) => (int) $id)->all();
            Cache::put(self::CACHE_KEY, $cached, 120);
        }

        if (empty($cached)) {
            return new Collection;
        }

        $records = BreakingNews::currentlyActive()
            ->whereIn('id', $cached)
            ->with(['article:id,title,slug,status,published_at,deleted_at'])
            ->get()
            ->filter(fn (BreakingNews $item) => $item->isEligibleForPublic())
            ->keyBy('id');

        $result = new Collection;
        foreach ($cached as $id) {
            if (isset($records[$id])) {
                $result->push($records[$id]);
            }
        }

        return $result;
    }

    /**
     * Invalidate breaking news cache immediately.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('topnews.breaking_news.active');
        Cache::forget('topnews.home.v2.data');
        Cache::forget('topnews.home.data');
    }
}
