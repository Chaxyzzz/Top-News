<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PublicContentCacheService
{
    /**
     * Invalidate all public homepage and discovery caches.
     */
    public function invalidateHomepage(): void
    {
        Cache::forget('topnews.home.v2.data');
        Cache::forget('topnews.home.data');
        Cache::forget('topnews.navigation.v2.categories');
        Cache::forget('topnews.navigation.categories');
        Cache::forget('topnews.trending.v2.limit_5');
        Cache::forget('topnews.trending.limit_5');
        Cache::forget('topnews.trending.v2.limit_10');
        Cache::forget('topnews.trending.limit_10');
        Cache::forget('topnews.popular.v2.today.limit_5');
        Cache::forget('topnews.popular.today.limit_5');
        Cache::forget('topnews.popular.v2.7days.limit_5');
        Cache::forget('topnews.popular.7days.limit_5');
        Cache::forget('topnews.popular.v2.30days.limit_5');
        Cache::forget('topnews.popular.30days.limit_5');
    }

    /**
     * Invalidate navigation cache specifically.
     */
    public function invalidateNavigation(): void
    {
        Cache::forget('topnews.navigation.v2.categories');
        Cache::forget('topnews.navigation.categories');
        Cache::forget('topnews.home.v2.data');
        Cache::forget('topnews.home.data');
    }
}
