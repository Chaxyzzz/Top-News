<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\DeviceDailyStat;
use App\Models\SiteDailyStat;
use App\Models\TrafficSourceDailyStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleViewService
{
    /**
     * Record a public view event for a published article.
     * Note: view tracking strictly does NOT mutate $article->updated_at.
     */
    public function recordView(Article $article): bool
    {
        try {
            // Only record views for genuinely published articles
            if ($article->status !== ArticleStatus::Published || ! $article->published_at || $article->published_at->isFuture()) {
                return false;
            }

            $today = Carbon::today()->toDateString();
            $articleSessionKey = "viewed_article_{$article->id}_{$today}";
            $siteSessionKey = "viewed_site_{$today}";

            $isArticleUnique = ! session()->has($articleSessionKey);
            $isSiteUnique = ! session()->has($siteSessionKey);

            if ($isArticleUnique) {
                session()->put($articleSessionKey, true);
            }

            if ($isSiteUnique) {
                session()->put($siteSessionKey, true);
            }

            $source = $this->resolveTrafficSource();
            $deviceType = $this->resolveDeviceType();

            // Update daily stats atomically within a transaction
            DB::transaction(function () use ($article, $today, $isArticleUnique, $isSiteUnique, $source, $deviceType) {
                // 1. Article Daily Stat
                $stat = ArticleDailyStat::firstOrCreate(
                    [
                        'article_id' => $article->id,
                        'date' => $today,
                    ],
                    [
                        'views' => 0,
                        'unique_views' => 0,
                    ]
                );

                $stat->increment('views');
                if ($isArticleUnique) {
                    $stat->increment('unique_views');
                }

                // 2. Increment article total views without touching updated_at timestamp!
                DB::table('articles')->where('id', $article->id)->increment('views_count');

                // 3. Site Daily Aggregate Stat
                $siteStat = SiteDailyStat::firstOrCreate(
                    ['date' => $today],
                    [
                        'page_views' => 0,
                        'unique_sessions' => 0,
                        'article_views' => 0,
                        'searches' => 0,
                        'comments_submitted' => 0,
                        'newsletter_subscriptions' => 0,
                    ]
                );
                $siteStat->increment('page_views');
                $siteStat->increment('article_views');
                if ($isSiteUnique) {
                    $siteStat->increment('unique_sessions');
                }

                // 4. Traffic Source Daily Stat
                $trafficStat = TrafficSourceDailyStat::firstOrCreate(
                    [
                        'date' => $today,
                        'source_type' => $source['type'],
                        'source_domain' => $source['domain'],
                    ],
                    [
                        'views' => 0,
                        'unique_views' => 0,
                    ]
                );
                $trafficStat->increment('views');
                if ($isArticleUnique) {
                    $trafficStat->increment('unique_views');
                }

                // 5. Device Daily Stat
                $deviceStat = DeviceDailyStat::firstOrCreate(
                    [
                        'date' => $today,
                        'device_type' => $deviceType,
                    ],
                    [
                        'views' => 0,
                    ]
                );
                $deviceStat->increment('views');
            });

            return true;
        } catch (\Throwable $e) {
            // Failsafe: Never disrupt reading experience if stats recording encounters an issue
            Log::warning("Failed to record view for article [{$article->id}]: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Resolve traffic source type and domain (hostname only, no path/query strings).
     *
     * @return array{type: string, domain: string}
     */
    public function resolveTrafficSource(): array
    {
        $referer = request()->headers->get('referer');

        if (! $referer) {
            return ['type' => 'direct', 'domain' => 'direct'];
        }

        $parsed = parse_url($referer);
        $host = isset($parsed['host']) ? strtolower($parsed['host']) : null;

        if (! $host) {
            return ['type' => 'direct', 'domain' => 'direct'];
        }

        $appHost = strtolower((string) parse_url(config('app.url', 'http://localhost'), PHP_URL_HOST));
        if ($host === $appHost || str_ends_with($host, '.'.$appHost)) {
            return ['type' => 'internal', 'domain' => $host];
        }

        // Search Engines
        $searchEngines = ['google.', 'bing.', 'yahoo.', 'duckduckgo.', 'yandex.', 'baidu.', 'ecosia.'];
        foreach ($searchEngines as $engine) {
            if (str_contains($host, $engine)) {
                return ['type' => 'search', 'domain' => $host];
            }
        }

        // Social Networks
        $socialNetworks = ['facebook.', 'fb.me', 'twitter.', 't.co', 'x.com', 'instagram.', 'linkedin.', 'tiktok.', 'whatsapp.', 'telegram.', 'youtube.'];
        foreach ($socialNetworks as $social) {
            if (str_contains($host, $social)) {
                return ['type' => 'social', 'domain' => $host];
            }
        }

        // External Referral
        return ['type' => 'referral', 'domain' => $host];
    }

    /**
     * Lightweight User-Agent device classification (Desktop, Mobile, Tablet, Unknown).
     */
    public function resolveDeviceType(): string
    {
        $userAgent = strtolower((string) request()->userAgent());

        if (empty($userAgent)) {
            return 'unknown';
        }

        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet';
        }

        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }
}
