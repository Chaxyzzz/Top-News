<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleDailyStat;
use App\Models\Category;
use App\Models\DeviceDailyStat;
use App\Models\SiteDailyStat;
use App\Models\TrafficSourceDailyStat;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Resolve date range from string preset or custom dates.
     *
     * @return array{start: Carbon, end: Carbon, preset: string, label: string}
     */
    public function resolveDateRange(string $preset = '7days', ?string $startDate = null, ?string $endDate = null): array
    {
        $today = Carbon::today();

        if ($preset === 'custom' && $startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();

                if ($start->gt($end)) {
                    [$start, $end] = [$end, $start];
                }

                // Bound range to max 365 days
                if ($start->diffInDays($end) > 365) {
                    $start = $end->copy()->subDays(365)->startOfDay();
                }

                return [
                    'start' => $start,
                    'end' => $end,
                    'preset' => 'custom',
                    'label' => $start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
                ];
            } catch (\Throwable) {
                // fallback to 7days on parse error
            }
        }

        return match ($preset) {
            'today' => [
                'start' => $today->copy()->startOfDay(),
                'end' => $today->copy()->endOfDay(),
                'preset' => 'today',
                'label' => 'Hari Ini',
            ],
            '30days' => [
                'start' => $today->copy()->subDays(29)->startOfDay(),
                'end' => $today->copy()->endOfDay(),
                'preset' => '30days',
                'label' => '30 Hari Terakhir',
            ],
            '90days' => [
                'start' => $today->copy()->subDays(89)->startOfDay(),
                'end' => $today->copy()->endOfDay(),
                'preset' => '90days',
                'label' => '90 Hari Terakhir',
            ],
            default => [
                'start' => $today->copy()->subDays(6)->startOfDay(),
                'end' => $today->copy()->endOfDay(),
                'preset' => '7days',
                'label' => '7 Hari Terakhir',
            ],
        };
    }

    /**
     * Get platform overview metrics.
     *
     * @return array<string, mixed>
     */
    public function getOverviewMetrics(Carbon $start, Carbon $end, ?int $authorId = null): array
    {
        $cacheKey = "analytics.overview.{$start->toDateString()}.{$end->toDateString()}.".($authorId ?? 'all');

        return Cache::remember($cacheKey, 300, function () use ($start, $end, $authorId) {
            $startDate = $start->toDateString();
            $endDate = $end->toDateString();

            // 1. Article Views Query
            $statsQuery = ArticleDailyStat::whereBetween('date', [$startDate, $endDate]);

            if ($authorId) {
                $statsQuery->whereHas('article', function ($q) use ($authorId) {
                    $q->where('author_id', $authorId);
                });
            }

            $totals = (clone $statsQuery)->selectRaw('COALESCE(SUM(views), 0) as total_views, COALESCE(SUM(unique_views), 0) as total_unique')->first();
            $totalViews = (int) ($totals->total_views ?? 0);
            $totalUnique = (int) ($totals->total_unique ?? 0);

            // 2. Published Articles Count
            $articlesQuery = Article::published()->whereBetween('published_at', [$start, $end]);
            if ($authorId) {
                $articlesQuery->where('author_id', $authorId);
            }
            $publishedCount = $articlesQuery->count();

            // 3. Site-Wide Metrics (Comments, Subscriptions, Pageviews)
            $siteTotals = SiteDailyStat::whereBetween('date', [$startDate, $endDate])
                ->selectRaw('COALESCE(SUM(comments_submitted), 0) as total_comments, COALESCE(SUM(newsletter_subscriptions), 0) as total_subs, COALESCE(SUM(page_views), 0) as total_pageviews')
                ->first();

            // 4. Daily Views Trend (Zero-Filled)
            $dailyRecords = (clone $statsQuery)
                ->select('date', DB::raw('SUM(views) as views'), DB::raw('SUM(unique_views) as unique_views'))
                ->groupBy('date')
                ->pluck('views', 'date')
                ->all();

            $trendLabels = [];
            $trendData = [];
            $period = CarbonPeriod::create($start, $end);

            foreach ($period as $dt) {
                $dStr = $dt->toDateString();
                $trendLabels[] = $dt->translatedFormat('d M');
                $trendData[] = (int) ($dailyRecords[$dStr] ?? 0);
            }

            // 5. Top 10 Articles in Period
            $topArticlesQuery = Article::published()
                ->select(['id', 'title', 'slug', 'category_id', 'author_id', 'published_at', 'views_count'])
                ->with(['category:id,name,slug,accent_color', 'author:id,name,username'])
                ->whereHas('dailyStats', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                })
                ->withSum(['dailyStats as period_views' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate]);
                }], 'views');

            if ($authorId) {
                $topArticlesQuery->where('author_id', $authorId);
            }

            $topArticles = $topArticlesQuery
                ->orderByDesc('period_views')
                ->limit(10)
                ->get();

            // 6. Category Performance
            $categoryBreakdown = $this->getCategoryBreakdown($start, $end);

            return [
                'total_views' => $totalViews,
                'total_unique' => $totalUnique,
                'published_articles' => $publishedCount,
                'total_comments' => (int) ($siteTotals->total_comments ?? 0),
                'total_subscriptions' => (int) ($siteTotals->total_subs ?? 0),
                'total_pageviews' => (int) ($siteTotals->total_pageviews ?? 0),
                'avg_views_per_article' => $publishedCount > 0 ? (int) round($totalViews / $publishedCount) : 0,
                'trend_labels' => $trendLabels,
                'trend_data' => $trendData,
                'top_articles' => $topArticles,
                'category_breakdown' => $categoryBreakdown,
            ];
        });
    }

    /**
     * Get paginated content metrics.
     */
    public function getContentMetrics(
        Carbon $start,
        Carbon $end,
        ?int $authorId = null,
        int $perPage = 20,
        string $sort = 'views'
    ): LengthAwarePaginator {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $query = Article::published()
            ->select(['id', 'title', 'slug', 'category_id', 'author_id', 'content_type', 'published_at', 'views_count'])
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username'])
            ->withCount(['comments' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->withSum(['dailyStats as period_views' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }], 'views')
            ->withSum(['dailyStats as period_unique' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }], 'unique_views');

        if ($authorId) {
            $query->where('author_id', $authorId);
        }

        switch ($sort) {
            case 'unique':
                $query->orderByDesc('period_unique')->orderByDesc('period_views');
                break;
            case 'comments':
                $query->orderByDesc('comments_count')->orderByDesc('period_views');
                break;
            case 'published':
                $query->latest('published_at');
                break;
            case 'views':
            default:
                $query->orderByDesc('period_views')->latest('published_at');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get category view shares in period.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getCategoryBreakdown(Carbon $start, Carbon $end): Collection
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $categories = Category::active()->get();
        $results = collect();
        $totalAllViews = 0;

        foreach ($categories as $cat) {
            $views = (int) ArticleDailyStat::whereBetween('date', [$startDate, $endDate])
                ->whereHas('article', function ($q) use ($cat) {
                    $q->where('category_id', $cat->id);
                })
                ->sum('views');

            $articlesCount = Article::published()
                ->where('category_id', $cat->id)
                ->whereBetween('published_at', [$start, $end])
                ->count();

            $totalAllViews += $views;

            $results->push([
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'accent_color' => $cat->accent_color ?? '#E50914',
                'views' => $views,
                'articles_count' => $articlesCount,
            ]);
        }

        // Compute percentages
        return $results->map(function ($item) use ($totalAllViews) {
            $item['percentage'] = $totalAllViews > 0 ? round(($item['views'] / $totalAllViews) * 100, 1) : 0;

            return $item;
        })->sortByDesc('views')->values();
    }

    /**
     * Get author performance rankings in period.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getAuthorPerformance(Carbon $start, Carbon $end): Collection
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $authors = User::editorialTeam()->get();
        $results = collect();

        foreach ($authors as $author) {
            $views = (int) ArticleDailyStat::whereBetween('date', [$startDate, $endDate])
                ->whereHas('article', function ($q) use ($author) {
                    $q->where('author_id', $author->id);
                })
                ->sum('views');

            $publishedCount = Article::published()
                ->where('author_id', $author->id)
                ->whereBetween('published_at', [$start, $end])
                ->count();

            if ($views > 0 || $publishedCount > 0) {
                $results->push([
                    'id' => $author->id,
                    'name' => $author->name,
                    'username' => $author->username,
                    'avatar' => $author->avatar_url,
                    'views' => $views,
                    'articles_count' => $publishedCount,
                    'avg_views' => $publishedCount > 0 ? (int) round($views / $publishedCount) : 0,
                ]);
            }
        }

        return $results->sortByDesc('views')->values();
    }

    /**
     * Get traffic source breakdown & referral domains.
     *
     * @return array{channels: Collection, top_referrers: Collection}
     */
    public function getTrafficSources(Carbon $start, Carbon $end): array
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $channels = TrafficSourceDailyStat::whereBetween('date', [$startDate, $endDate])
            ->select('source_type', DB::raw('SUM(views) as views'), DB::raw('SUM(unique_views) as unique_views'))
            ->groupBy('source_type')
            ->orderByDesc('views')
            ->get();

        $totalViews = $channels->sum('views');

        $channelsWithPercent = $channels->map(function ($item) use ($totalViews) {
            return [
                'type' => $item->source_type,
                'label' => match ($item->source_type) {
                    'direct' => 'Langsung / Direct',
                    'search' => 'Mesin Pencari (Organik)',
                    'social' => 'Media Sosial',
                    'referral' => 'Rujukan Eksternal',
                    'internal' => 'Sirkulasi Internal',
                    default => 'Lainnya / Tidak Diketahui',
                },
                'views' => (int) $item->views,
                'unique_views' => (int) $item->unique_views,
                'percentage' => $totalViews > 0 ? round(($item->views / $totalViews) * 100, 1) : 0,
            ];
        });

        $topReferrers = TrafficSourceDailyStat::whereBetween('date', [$startDate, $endDate])
            ->where('source_domain', '!=', 'direct')
            ->select('source_domain', 'source_type', DB::raw('SUM(views) as views'), DB::raw('SUM(unique_views) as unique_views'))
            ->groupBy('source_domain', 'source_type')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        return [
            'channels' => $channelsWithPercent,
            'top_referrers' => $topReferrers,
            'total_views' => $totalViews,
        ];
    }

    /**
     * Get device type breakdown.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getDeviceBreakdown(Carbon $start, Carbon $end): Collection
    {
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();

        $devices = DeviceDailyStat::whereBetween('date', [$startDate, $endDate])
            ->select('device_type', DB::raw('SUM(views) as views'))
            ->groupBy('device_type')
            ->orderByDesc('views')
            ->get();

        $total = $devices->sum('views');

        return $devices->map(function ($d) use ($total) {
            return [
                'type' => $d->device_type,
                'label' => match ($d->device_type) {
                    'desktop' => 'Komputer (Desktop/Laptop)',
                    'mobile' => 'Ponsel Pintar (Mobile)',
                    'tablet' => 'Tablet',
                    default => 'Tidak Terdeteksi',
                },
                'views' => (int) $d->views,
                'percentage' => $total > 0 ? round(($d->views / $total) * 100, 1) : 0,
            ];
        });
    }
}
