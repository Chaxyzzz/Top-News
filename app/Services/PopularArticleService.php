<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PopularArticleService
{
    /**
     * Get most-read published articles over a given timeframe.
     *
     * @param  string  $period  'today', '7days', '30days'
     * @return Collection<int, Article>
     */
    public function getPopular(string $period = '7days', int $limit = 5): Collection
    {
        $cacheKey = "topnews.popular.v2.{$period}.limit_{$limit}";

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidCachePayload($cached)) {
            Cache::forget($cacheKey);
            $cached = $this->calculatePopularPayload($period, $limit);
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
     * Calculate popular ranking payload as primitive array.
     *
     * @return array<int, array{id: int, period_views?: ?int}>
     */
    protected function calculatePopularPayload(string $period, int $limit): array
    {
        $startDate = match ($period) {
            'today', '1day' => Carbon::today()->toDateString(),
            '30days', 'month' => Carbon::now()->subDays(30)->toDateString(),
            default => Carbon::now()->subDays(7)->toDateString(),
        };

        $cardFields = [
            'articles.id', 'articles.uuid', 'articles.title', 'articles.slug',
            'articles.subtitle', 'articles.excerpt', 'articles.category_id',
            'articles.author_id', 'articles.featured_image', 'articles.featured_image_alt',
            'articles.published_at', 'articles.reading_time', 'articles.views_count',
        ];

        $results = Article::published()
            ->select($cardFields)
            ->join('article_daily_stats', function ($join) use ($startDate) {
                $join->on('articles.id', '=', 'article_daily_stats.article_id')
                    ->where('article_daily_stats.date', '>=', $startDate);
            })
            ->groupBy(array_map(fn ($f) => str_contains($f, '.') ? $f : 'articles.'.$f, $cardFields))
            ->selectRaw('SUM(article_daily_stats.views) as period_views')
            ->having('period_views', '>', 0)
            ->orderByDesc('period_views')
            ->orderByDesc('articles.published_at')
            ->limit($limit)
            ->get();

        if ($results->isEmpty()) {
            $results = Article::published()
                ->select([
                    'id', 'uuid', 'title', 'slug', 'subtitle', 'excerpt',
                    'category_id', 'author_id', 'featured_image', 'featured_image_alt',
                    'published_at', 'reading_time', 'views_count',
                ])
                ->where('views_count', '>', 0)
                ->orderByDesc('views_count')
                ->orderByDesc('published_at')
                ->limit($limit)
                ->get();
        }

        return $results->map(fn ($a) => [
            'id' => (int) $a->id,
            'period_views' => isset($a->period_views) ? (int) $a->period_views : null,
        ])->all();
    }

    /**
     * Rehydrate published Article models preserving cached ranking order.
     *
     * @param  array<int, array{id: int, period_views?: ?int}>  $cachedData
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
                'id', 'uuid', 'title', 'slug', 'subtitle', 'excerpt',
                'category_id', 'author_id', 'featured_image', 'featured_image_alt',
                'published_at', 'reading_time', 'views_count',
            ])
            ->with([
                'category:id,name,slug,accent_color',
                'author:id,name,username,avatar',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $result = new Collection;

        foreach ($cachedData as $item) {
            $articleId = $item['id'] ?? null;
            if ($articleId && isset($articles[$articleId])) {
                $article = $articles[$articleId];
                if (isset($item['period_views']) && $item['period_views'] !== null) {
                    $article->period_views = $item['period_views'];
                }
                $result->push($article);
            }
        }

        return $result;
    }
}
