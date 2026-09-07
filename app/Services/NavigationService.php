<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public const CACHE_PREFIX = 'topnews.menu.v2.';

    /**
     * Get active menu with eager loaded items and categories.
     */
    public function getMenu(string $key): ?Menu
    {
        $cacheKey = self::CACHE_PREFIX.$key;

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

        if ($cached !== null && $cached instanceof Menu) {
            return $cached;
        }

        Cache::forget($cacheKey);

        return Cache::remember($cacheKey, 3600, function () use ($key) {
            return Menu::where('key', $key)
                ->with([
                    'activeItems' => function ($q) {
                        $q->with([
                            'category:id,name,slug,accent_color',
                            'children.category:id,name,slug,accent_color',
                        ]);
                    },
                ])
                ->first();
        });
    }

    /**
     * Get active navigation categories for header.
     *
     * @return Collection<int, Category>
     */
    public function getNavigationCategories(): Collection
    {
        $cacheKey = 'topnews.navigation.v2.categories';

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class) {
            Cache::forget($cacheKey);
            $categories = Category::navigation()->limit(9)->get();
            $cached = $categories->pluck('id')->map(fn ($id) => (int) $id)->all();
            Cache::put($cacheKey, $cached, 3600);
        }

        if (empty($cached)) {
            return new Collection;
        }

        $cats = Category::navigation()
            ->whereIn('id', $cached)
            ->get()
            ->keyBy('id');

        $result = new Collection;
        foreach ($cached as $id) {
            if (isset($cats[$id])) {
                $result->push($cats[$id]);
            }
        }

        return $result;
    }

    /**
     * Clear menu caches.
     */
    public function clearCache(?string $key = null): void
    {
        Cache::forget('topnews.navigation.v2.categories');
        Cache::forget('topnews.navigation.categories');

        if ($key) {
            Cache::forget(self::CACHE_PREFIX.$key);
            Cache::forget('topnews.menu.'.$key);
        } else {
            Cache::forget(self::CACHE_PREFIX.'primary');
            Cache::forget(self::CACHE_PREFIX.'footer');
            Cache::forget('topnews.menu.primary');
            Cache::forget('topnews.menu.footer');
        }
    }
}
