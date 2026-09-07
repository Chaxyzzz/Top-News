<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class RelatedArticleService
{
    /**
     * Common fields required for article cards.
     *
     * @var array<int, string>
     */
    protected array $cardFields = [
        'id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'featured_image',
        'featured_image_alt',
        'featured_image_caption',
        'content_type',
        'is_featured',
        'is_breaking',
        'is_editor_choice',
        'is_sponsored',
        'homepage_priority',
        'reading_time',
        'views_count',
        'published_at',
        'created_at',
    ];

    /**
     * Get contextually related published articles for a given article.
     *
     * Ranking criteria:
     * 1. Articles in the same category and/or sharing overlapping tags.
     * 2. Number of overlapping tags adds relevance weight.
     * 3. Recency via published_at.
     * 4. Exclude current article.
     *
     * @return Collection<int, Article>
     */
    /**
     * Get contextually related published articles for a given article.
     *
     * Ranking criteria:
     * 1. Articles in the same category and/or sharing overlapping tags.
     * 2. Number of overlapping tags adds relevance weight.
     * 3. Recency via published_at.
     * 4. Exclude current article.
     *
     * @return Collection<int, Article>
     */
    public function getRelatedArticles(Article $article, int $limit = 3): Collection
    {
        $cacheKey = "topnews.article.{$article->id}.related.v2.{$limit}";

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidIdArray($cached)) {
            Cache::forget($cacheKey);
            $cached = $this->calculateRelatedArticleIds($article, $limit);
            Cache::put($cacheKey, $cached, 600);
        }

        return $this->rehydrateArticlesByIds($cached);
    }

    /**
     * Get curated reading recommendations ("Pilihan Bacaan Lainnya") with category diversity.
     *
     * @param  array<int, int>  $excludeIds
     * @return Collection<int, Article>
     */
    public function getRecommendedArticles(Article $article, array $excludeIds = [], int $limit = 4): Collection
    {
        $allExclude = array_unique(array_merge([$article->id], $excludeIds));
        $cacheKey = 'topnews.article.'.$article->id.'.recommended.v2.'.implode('-', $allExclude).".{$limit}";

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidIdArray($cached)) {
            Cache::forget($cacheKey);
            $cached = $this->calculateRecommendedArticleIds($article, $allExclude, $limit);
            Cache::put($cacheKey, $cached, 600);
        }

        return $this->rehydrateArticlesByIds($cached);
    }

    /**
     * Check if cached payload is an array of integer/numeric IDs.
     */
    protected function isValidIdArray(mixed $cached): bool
    {
        if (! is_array($cached)) {
            return false;
        }

        foreach ($cached as $id) {
            if (! is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate related article IDs.
     *
     * @return list<int>
     */
    protected function calculateRelatedArticleIds(Article $article, int $limit): array
    {
        $tagIds = $article->tags->pluck('id')->all();

        $query = Article::published()
            ->where('articles.id', '!=', $article->id)
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar']);

        if (! empty($tagIds)) {
            $query->leftJoin('article_tag', function ($join) use ($tagIds) {
                $join->on('articles.id', '=', 'article_tag.article_id')
                    ->whereIn('article_tag.tag_id', $tagIds);
            })
                ->selectRaw('count(article_tag.tag_id) as tag_overlap')
                ->groupBy(array_map(fn ($f) => 'articles.'.$f, $this->cardFields))
                ->orderByRaw('CASE WHEN articles.category_id = ? THEN 1 ELSE 0 END DESC', [$article->category_id])
                ->orderByDesc('tag_overlap')
                ->latest('articles.published_at');
        } else {
            $query->where('category_id', $article->category_id)
                ->latest('published_at');
        }

        $results = $query->limit($limit)->get();

        if ($results->count() < $limit) {
            $needed = $limit - $results->count();
            $excludeIds = array_merge([$article->id], $results->pluck('id')->all());

            $fallbacks = Article::published()
                ->whereNotIn('id', $excludeIds)
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                ->latest('published_at')
                ->limit($needed)
                ->get();

            $results = $results->concat($fallbacks);
        }

        return $results->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Calculate recommended article IDs.
     *
     * @param  list<int>  $allExclude
     * @return list<int>
     */
    protected function calculateRecommendedArticleIds(Article $article, array $allExclude, int $limit): array
    {
        $recommendations = Article::published()
            ->whereNotIn('id', $allExclude)
            ->where(function ($q) use ($article) {
                $q->where('is_editor_choice', true)
                    ->orWhere('is_featured', true)
                    ->orWhere('homepage_priority', '>', 0)
                    ->orWhere('category_id', '!=', $article->category_id);
            })
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->orderByDesc('is_editor_choice')
            ->orderByDesc('homepage_priority')
            ->latest('published_at')
            ->limit($limit)
            ->get();

        if ($recommendations->count() < $limit) {
            $needed = $limit - $recommendations->count();
            $moreExclude = array_merge($allExclude, $recommendations->pluck('id')->all());

            $more = Article::published()
                ->whereNotIn('id', $moreExclude)
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                ->latest('published_at')
                ->limit($needed)
                ->get();

            $recommendations = $recommendations->concat($more);
        }

        return $recommendations->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Rehydrate published Article models preserving cached order.
     *
     * @param  list<int>  $ids
     * @return Collection<int, Article>
     */
    protected function rehydrateArticlesByIds(array $ids): Collection
    {
        $validIds = array_filter(array_map('intval', $ids));
        if (empty($validIds)) {
            return new Collection;
        }

        $articles = Article::published()
            ->whereIn('id', $validIds)
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->get()
            ->keyBy('id');

        $result = new Collection;
        foreach ($validIds as $id) {
            if (isset($articles[$id])) {
                $result->push($articles[$id]);
            }
        }

        return $result;
    }

    /**
     * Get chronological Previous and Next published stories relative to the current article.
     *
     * Rule:
     * - Previous = older published article (published_at < current, or id < current if same time)
     * - Next = newer published article (published_at > current, or id > current if same time)
     * - Prioritizes the same category; falls back to global publication stream.
     *
     * @return array{previous: ?Article, next: ?Article}
     */
    public function getPreviousAndNext(Article $article): array
    {
        $publishedAt = $article->published_at ?? $article->created_at;

        // 1. Previous Article (Older)
        $previous = Article::published()
            ->where('category_id', $article->category_id)
            ->where(function ($q) use ($publishedAt, $article) {
                $q->where('published_at', '<', $publishedAt)
                    ->orWhere(function ($q2) use ($publishedAt, $article) {
                        $q2->where('published_at', '=', $publishedAt)
                            ->where('id', '<', $article->id);
                    });
            })
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color'])
            ->latest('published_at')
            ->orderByDesc('id')
            ->first();

        if (! $previous) {
            // Global fallback for previous
            $previous = Article::published()
                ->where('id', '!=', $article->id)
                ->where(function ($q) use ($publishedAt, $article) {
                    $q->where('published_at', '<', $publishedAt)
                        ->orWhere(function ($q2) use ($publishedAt, $article) {
                            $q2->where('published_at', '=', $publishedAt)
                                ->where('id', '<', $article->id);
                        });
                })
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color'])
                ->latest('published_at')
                ->orderByDesc('id')
                ->first();
        }

        // 2. Next Article (Newer)
        $next = Article::published()
            ->where('category_id', $article->category_id)
            ->where(function ($q) use ($publishedAt, $article) {
                $q->where('published_at', '>', $publishedAt)
                    ->orWhere(function ($q2) use ($publishedAt, $article) {
                        $q2->where('published_at', '=', $publishedAt)
                            ->where('id', '>', $article->id);
                    });
            })
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color'])
            ->oldest('published_at')
            ->orderBy('id')
            ->first();

        if (! $next) {
            // Global fallback for next
            $next = Article::published()
                ->where('id', '!=', $article->id)
                ->where(function ($q) use ($publishedAt, $article) {
                    $q->where('published_at', '>', $publishedAt)
                        ->orWhere(function ($q2) use ($publishedAt, $article) {
                            $q2->where('published_at', '=', $publishedAt)
                                ->where('id', '>', $article->id);
                        });
                })
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color'])
                ->oldest('published_at')
                ->orderBy('id')
                ->first();
        }

        return [
            'previous' => $previous,
            'next' => $next,
        ];
    }
}
