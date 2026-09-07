<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TrendingArticleService
{
    /**
     * Common fields required for article cards.
     *
     * @var list<string>
     */
    protected array $cardFields = [
        'articles.id',
        'articles.uuid',
        'articles.title',
        'articles.slug',
        'articles.subtitle',
        'articles.category_id',
        'articles.author_id',
        'articles.featured_image',
        'articles.featured_image_alt',
        'articles.published_at',
        'articles.reading_time',
        'articles.views_count',
    ];

    /**
     * Get top trending articles using velocity and freshness decay ranking.
     * Formula: score = recent_views / pow(hours_since_publish + 2, 1.2)
     *
     * @return Collection<int, Article>
     */
    public function getTrending(int $limit = 5): Collection
    {
        $cacheKey = "topnews.trending.v2.limit_{$limit}";

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidCachePayload($cached)) {
            Cache::forget($cacheKey);
            $cached = $this->calculateTrendingPayload($limit);
            Cache::put($cacheKey, $cached, 600);
        }

        return $this->rehydrateArticles($cached);
    }

    /**
     * Check if cached payload is valid.
     */
    protected function isValidCachePayload(mixed $cached): bool
    {
        if (! is_array($cached)) {
            return false;
        }

        foreach ($cached as $item) {
            if (! is_array($item) || ! isset($item['id'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate trending ranking payload as primitive array.
     *
     * @return array<int, array{id: int, trending_score: float, recent_views: int}>
     */
    protected function calculateTrendingPayload(int $limit): array
    {
        $since = Carbon::now()->subDays(2)->toDateString();

        $candidates = Article::published()
            ->select($this->cardFields)
            ->leftJoin('article_daily_stats', function ($join) use ($since) {
                $join->on('articles.id', '=', 'article_daily_stats.article_id')
                    ->where('article_daily_stats.date', '>=', $since);
            })
            ->groupBy(array_map(fn ($f) => str_contains($f, '.') ? $f : 'articles.'.$f, $this->cardFields))
            ->selectRaw('COALESCE(SUM(article_daily_stats.views), 0) as recent_views')
            ->get();

        if ($candidates->isEmpty()) {
            return [];
        }

        $now = Carbon::now();

        $ranked = $candidates->map(function ($article) use ($now) {
            $hoursSincePublish = max(0, $article->published_at ? $article->published_at->diffInHours($now) : 0);
            $recentViews = (int) $article->recent_views;

            $effectiveViews = $recentViews > 0 ? $recentViews : ($article->views_count > 0 ? 1 : 0);
            $decayFactor = pow($hoursSincePublish + 2, 1.2);
            $article->trending_score = $effectiveViews / $decayFactor;

            return $article;
        })
            ->sortByDesc(fn ($a) => [$a->trending_score, $a->recent_views, $a->published_at])
            ->values()
            ->take($limit);

        return $ranked->map(fn ($a) => [
            'id' => (int) $a->id,
            'trending_score' => (float) $a->trending_score,
            'recent_views' => (int) $a->recent_views,
        ])->all();
    }

    /**
     * Rehydrate published Article models preserving cached ranking order.
     *
     * @param  array<int, array{id: int, trending_score?: float, recent_views?: int}>  $cachedData
     * @return Collection<int, Article>
     */
    protected function rehydrateArticles(array $cachedData): Collection
    {
        if (empty($cachedData)) {
            return new Collection;
        }

        $ids = array_filter(array_column($cachedData, 'id'));

        if (empty($ids)) {
            return new Collection;
        }

        $articles = Article::published()
            ->select([
                'articles.id',
                'articles.uuid',
                'articles.title',
                'articles.slug',
                'articles.subtitle',
                'articles.category_id',
                'articles.author_id',
                'articles.featured_image',
                'articles.featured_image_alt',
                'articles.published_at',
                'articles.reading_time',
                'articles.views_count',
            ])
            ->with([
                'category:id,name,slug,accent_color',
                'author:id,name,username,avatar',
            ])
            ->whereIn('articles.id', $ids)
            ->get()
            ->keyBy('id');

        $result = new Collection;

        foreach ($cachedData as $item) {
            $articleId = $item['id'] ?? null;
            if ($articleId && isset($articles[$articleId])) {
                $article = $articles[$articleId];
                if (isset($item['trending_score'])) {
                    $article->trending_score = $item['trending_score'];
                }
                if (isset($item['recent_views'])) {
                    $article->recent_views = $item['recent_views'];
                }
                $result->push($article);
            }
        }

        return $result;
    }
}
