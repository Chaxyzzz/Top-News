<?php

namespace App\Services;

use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as EmptyPaginator;
use Illuminate\Support\Str;

class ArticleSearchService
{
    /**
     * Perform public article search with relevance scoring, filtering, and sorting.
     */
    public function search(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $rawQuery = (string) $request->input('q', '');
        $query = $this->sanitizeQuery($rawQuery);

        if (mb_strlen($query) < 2) {
            return new EmptyPaginator([], 0, $perPage, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        // Strictly public published articles
        $articles = Article::query()
            ->published()
            ->with(['category', 'author', 'featuredMedia', 'tags']);

        // Apply Search Term Filtering & Relevance
        $this->applySearchQuery($articles, $query);

        // Apply Optional Filters
        $this->applyCategoryFilter($articles, $request->input('category'));
        $this->applyTypeFilter($articles, $request->input('type'));
        $this->applyDateFilter($articles, $request->input('date'));

        // Apply Sorting
        $this->applySorting($articles, $request->input('sort', 'relevance'), $query);

        return $articles->paginate($perPage)->withQueryString();
    }

    /**
     * Sanitize and normalize user search query.
     */
    public function sanitizeQuery(string $query): string
    {
        // Trim and collapse excessive internal whitespace
        $clean = preg_replace('/\s+/', ' ', trim($query));

        // Limit maximum character length to prevent pathological requests
        return mb_substr($clean, 0, 200);
    }

    /**
     * Apply query condition across public content fields.
     */
    protected function applySearchQuery(Builder $query, string $term): void
    {
        $searchTerm = '%'.$term.'%';

        $query->where(function (Builder $q) use ($searchTerm, $term) {
            $q->where('title', 'like', $searchTerm)
                ->orWhere('subtitle', 'like', $searchTerm)
                ->orWhere('excerpt', 'like', $searchTerm)
                ->orWhere('content', 'like', $searchTerm)
                ->orWhereHas('category', function (Builder $catQuery) use ($searchTerm) {
                    $catQuery->where('name', 'like', $searchTerm);
                })
                ->orWhereHas('tags', function (Builder $tagQuery) use ($term, $searchTerm) {
                    $tagQuery->where('name', 'like', $searchTerm)
                        ->orWhere('slug', 'like', '%'.Str::slug($term).'%');
                })
                ->orWhereHas('author', function (Builder $authorQuery) use ($searchTerm) {
                    $authorQuery->where('name', 'like', $searchTerm);
                });
        });
    }

    /**
     * Filter by Category slug or ID.
     */
    protected function applyCategoryFilter(Builder $query, ?string $category): void
    {
        if (empty($category)) {
            return;
        }

        if (is_numeric($category)) {
            $query->where('category_id', (int) $category);
        } else {
            $query->whereHas('category', function (Builder $q) use ($category) {
                $q->where('slug', $category);
            });
        }
    }

    /**
     * Filter by Content Type.
     */
    protected function applyTypeFilter(Builder $query, ?string $type): void
    {
        if (empty($type)) {
            return;
        }

        $validTypes = array_map(fn (ArticleType $case) => $case->value, ArticleType::cases());

        if (in_array($type, $validTypes, true)) {
            $query->where('content_type', $type);
        }
    }

    /**
     * Filter by Publication Date (WIB Asia/Jakarta).
     */
    protected function applyDateFilter(Builder $query, ?string $dateRange): void
    {
        if (empty($dateRange)) {
            return;
        }

        $now = Carbon::now('Asia/Jakarta');

        match ($dateRange) {
            'today' => $query->where('published_at', '>=', $now->copy()->startOfDay()),
            'last_7_days' => $query->where('published_at', '>=', $now->copy()->subDays(7)),
            'last_30_days' => $query->where('published_at', '>=', $now->copy()->subDays(30)),
            'this_year' => $query->where('published_at', '>=', $now->copy()->startOfYear()),
            default => null,
        };
    }

    /**
     * Apply sorting with weighted relevance calculation.
     */
    protected function applySorting(Builder $query, ?string $sort, string $term): void
    {
        $searchTerm = '%'.$term.'%';

        switch ($sort) {
            case 'newest':
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;

            case 'oldest':
                $query->orderBy('published_at', 'asc')->orderBy('id', 'asc');
                break;

            case 'most_read':
                $query->orderBy('views_count', 'desc')->orderBy('published_at', 'desc');
                break;

            case 'relevance':
            default:
                // Weighted relevance priority: Title > Subtitle > Excerpt > Content
                $query->select('articles.*')
                    ->selectRaw('
                        (CASE WHEN title LIKE ? THEN 100 ELSE 0 END) +
                        (CASE WHEN subtitle LIKE ? THEN 50 ELSE 0 END) +
                        (CASE WHEN excerpt LIKE ? THEN 25 ELSE 0 END) +
                        (CASE WHEN content LIKE ? THEN 10 ELSE 0 END) AS relevance_score
                    ', [$searchTerm, $searchTerm, $searchTerm, $searchTerm])
                    ->orderBy('relevance_score', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->orderBy('id', 'desc');
                break;
        }
    }

    /**
     * Get list of active categories that have published articles.
     *
     * @return Collection<int, Category>
     */
    public function getActiveCategories()
    {
        return Category::query()
            ->active()
            ->whereHas('articles', function (Builder $q) {
                $q->published();
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }
}
