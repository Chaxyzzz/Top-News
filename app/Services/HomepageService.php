<?php

namespace App\Services;

use App\Enums\ArticleType;
use App\Enums\HomepageSectionType;
use App\Enums\HomepageSourceType;
use App\Models\Article;
use App\Models\Category;
use App\Models\HomepageSection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class HomepageService
{
    public function __construct(
        protected TrendingArticleService $trendingService,
        protected PopularArticleService $popularService,
        protected HomepageConfigurationService $configService,
        protected BreakingNewsService $breakingNewsService
    ) {}

    /**
     * Common fields required for public cards (Rule 7: Query Efficiency).
     *
     * @var list<string>
     */
    protected array $cardFields = [
        'id',
        'uuid',
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'content_type',
        'category_id',
        'author_id',
        'featured_image',
        'featured_image_caption',
        'featured_image_alt',
        'is_featured',
        'is_breaking',
        'is_editor_choice',
        'is_sponsored',
        'sponsor_name',
        'sponsor_url',
        'homepage_priority',
        'published_at',
        'reading_time',
        'views_count',
    ];

    /**
     * Assemble full homepage dataset driven by Homepage CMS configuration.
     *
     * @return array<string, mixed>
     */
    /**
     * Assemble full homepage dataset driven by Homepage CMS configuration.
     *
     * @return array<string, mixed>
     */
    public function getHomepageData(): array
    {
        $cacheKey = 'topnews.home.v2.data';

        $cached = null;
        try {
            $cached = Cache::get($cacheKey);
        } catch (\Throwable) {
            Cache::forget($cacheKey);
            $cached = null;
        }

        if (! is_array($cached) || $cached instanceof \__PHP_Incomplete_Class || ! $this->isValidHomeCachePayload($cached)) {
            Cache::forget($cacheKey);
            $cached = $this->calculateHomePayload();
            Cache::put($cacheKey, $cached, 600);
        }

        return $this->rehydrateHomepageData($cached);
    }

    /**
     * Validate structure of home cache payload.
     */
    protected function isValidHomeCachePayload(mixed $cached): bool
    {
        return is_array($cached)
            && isset($cached['latest_news_ids'])
            && is_array($cached['latest_news_ids']);
    }

    /**
     * Calculate primitive ID map for homepage sections.
     *
     * @return array<string, mixed>
     */
    protected function calculateHomePayload(): array
    {
        $excludeIds = [];

        // 1. Breaking News article IDs
        $breakingNewsIds = Article::published()
            ->breaking()
            ->latest('published_at')
            ->limit(5)
            ->pluck('id')
            ->all();

        // 2. Active Sections from Homepage CMS
        $activeSections = $this->configService->getActiveSections();
        $heroConfig = $activeSections->firstWhere('key', 'hero') ?? $activeSections->firstWhere('section_type', HomepageSectionType::Hero);

        // 3. Hero Selection
        $leadStoryId = null;
        $supportingStoryIds = [];

        if ($heroConfig && $heroConfig->source_type === HomepageSourceType::Manual) {
            $curatedHeroArticles = $heroConfig->curatedArticles()
                ->published()
                ->limit($heroConfig->item_limit ?? 5)
                ->get();

            if ($curatedHeroArticles->isNotEmpty()) {
                $leadStoryId = $curatedHeroArticles->first()->id;
                $supportingStoryIds = $curatedHeroArticles->slice(1)->pluck('id')->all();
            }
        }

        if (! $leadStoryId) {
            $leadStory = Article::published()
                ->orderByDesc('homepage_priority')
                ->orderByDesc('is_featured')
                ->latest('published_at')
                ->first();

            if ($leadStory) {
                $leadStoryId = $leadStory->id;
            }
        }

        if ($leadStoryId) {
            $excludeIds[] = $leadStoryId;
        }

        if (empty($supportingStoryIds)) {
            $supporting = Article::published()
                ->whereNotIn('id', $excludeIds)
                ->where(function ($q) {
                    $q->where('is_featured', true)
                        ->orWhere('homepage_priority', '>', 0);
                })
                ->orderByDesc('homepage_priority')
                ->latest('published_at')
                ->limit(4)
                ->get();

            if ($supporting->count() < 3) {
                $needed = 3 - $supporting->count();
                $additional = Article::published()
                    ->whereNotIn('id', array_merge($excludeIds, $supporting->pluck('id')->all()))
                    ->latest('published_at')
                    ->limit($needed)
                    ->get();

                $supporting = $supporting->concat($additional);
            }

            $supportingStoryIds = $supporting->pluck('id')->all();
        }

        foreach ($supportingStoryIds as $sId) {
            $excludeIds[] = $sId;
        }

        // 4. Latest News Feed
        $latestConfig = $activeSections->firstWhere('key', 'latest') ?? $activeSections->firstWhere('section_type', HomepageSectionType::Latest);
        $latestLimit = $latestConfig ? $latestConfig->item_limit : 8;

        $latestNewsIds = Article::published()
            ->whereNotIn('id', $excludeIds)
            ->latest('published_at')
            ->limit($latestLimit)
            ->pluck('id')
            ->all();

        // 5. Editor's Choice
        $editorsChoiceIds = Article::published()
            ->editorChoice()
            ->latest('published_at')
            ->limit(6)
            ->pluck('id')
            ->all();

        // 6. Opinion Columns
        $opinionColumnIds = Article::published()
            ->opinion()
            ->latest('published_at')
            ->limit(3)
            ->pluck('id')
            ->all();

        // 7. Category Highlights
        $homepageCategories = Category::homepage()->get();
        $categorySections = [];

        foreach ($homepageCategories as $category) {
            $catArticleIds = $category->articles()
                ->published()
                ->latest('published_at')
                ->limit(4)
                ->pluck('articles.id')
                ->all();

            if (! empty($catArticleIds)) {
                $categorySections[] = [
                    'category_id' => $category->id,
                    'article_ids' => $catArticleIds,
                ];
            }
        }

        // 8. Video & Photo Story Articles
        $videoArticleIds = Article::published()
            ->where('content_type', ArticleType::Video->value)
            ->latest('published_at')
            ->limit(4)
            ->pluck('id')
            ->all();

        $photoArticleIds = Article::published()
            ->where('content_type', ArticleType::PhotoStory->value)
            ->latest('published_at')
            ->limit(4)
            ->pluck('id')
            ->all();

        return [
            'breaking_news_ids' => $breakingNewsIds,
            'lead_story_id' => $leadStoryId,
            'supporting_story_ids' => $supportingStoryIds,
            'latest_news_ids' => $latestNewsIds,
            'editors_choice_ids' => $editorsChoiceIds,
            'opinion_column_ids' => $opinionColumnIds,
            'category_sections' => $categorySections,
            'video_article_ids' => $videoArticleIds,
            'photo_article_ids' => $photoArticleIds,
        ];
    }

    /**
     * Rehydrate full homepage dataset from primitive payload.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function rehydrateHomepageData(array $payload): array
    {
        // Collect all article IDs needed across all sections
        $allIds = [];
        if (! empty($payload['breaking_news_ids'])) {
            $allIds = array_merge($allIds, $payload['breaking_news_ids']);
        }
        if (! empty($payload['lead_story_id'])) {
            $allIds[] = $payload['lead_story_id'];
        }
        if (! empty($payload['supporting_story_ids'])) {
            $allIds = array_merge($allIds, $payload['supporting_story_ids']);
        }
        if (! empty($payload['latest_news_ids'])) {
            $allIds = array_merge($allIds, $payload['latest_news_ids']);
        }
        if (! empty($payload['editors_choice_ids'])) {
            $allIds = array_merge($allIds, $payload['editors_choice_ids']);
        }
        if (! empty($payload['opinion_column_ids'])) {
            $allIds = array_merge($allIds, $payload['opinion_column_ids']);
        }
        if (! empty($payload['video_article_ids'])) {
            $allIds = array_merge($allIds, $payload['video_article_ids']);
        }
        if (! empty($payload['photo_article_ids'])) {
            $allIds = array_merge($allIds, $payload['photo_article_ids']);
        }
        if (! empty($payload['category_sections'])) {
            foreach ($payload['category_sections'] as $cs) {
                if (! empty($cs['article_ids'])) {
                    $allIds = array_merge($allIds, $cs['article_ids']);
                }
            }
        }

        $allIds = array_unique(array_filter(array_map('intval', $allIds)));

        $articles = empty($allIds) ? collect() : Article::published()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->whereIn('id', $allIds)
            ->get()
            ->keyBy('id');

        $mapIdsToCollection = function (array $ids) use ($articles) {
            $col = new Collection;
            foreach ($ids as $id) {
                if (isset($articles[$id])) {
                    $col->push($articles[$id]);
                }
            }

            return $col;
        };

        $breakingNews = $mapIdsToCollection($payload['breaking_news_ids'] ?? []);
        $leadStory = ! empty($payload['lead_story_id']) && isset($articles[$payload['lead_story_id']]) ? $articles[$payload['lead_story_id']] : null;
        $supportingStories = $mapIdsToCollection($payload['supporting_story_ids'] ?? []);
        $latestNews = $mapIdsToCollection($payload['latest_news_ids'] ?? []);
        $editorsChoice = $mapIdsToCollection($payload['editors_choice_ids'] ?? []);
        $opinionColumns = $mapIdsToCollection($payload['opinion_column_ids'] ?? []);
        $videoArticles = $mapIdsToCollection($payload['video_article_ids'] ?? []);
        $photoArticles = $mapIdsToCollection($payload['photo_article_ids'] ?? []);

        // Dynamic Trending & Popular
        $trendingNews = $this->trendingService->getTrending(5);
        $popularNews = $this->popularService->getPopular('7days', 5);
        $breakingItems = $this->breakingNewsService->getActiveBreakingNews();

        // Category Sections
        $categorySections = [];
        if (! empty($payload['category_sections'])) {
            $categoryIds = array_column($payload['category_sections'], 'category_id');
            $categories = Category::whereIn('id', $categoryIds)->get()->keyBy('id');

            foreach ($payload['category_sections'] as $cs) {
                $catId = $cs['category_id'] ?? null;
                if ($catId && isset($categories[$catId])) {
                    $catArticles = $mapIdsToCollection($cs['article_ids'] ?? []);
                    if ($catArticles->isNotEmpty()) {
                        $categorySections[] = [
                            'category' => $categories[$catId],
                            'articles' => $catArticles,
                        ];
                    }
                }
            }
        }

        // Active CMS Sections
        $activeSections = $this->configService->getActiveSections();
        $resolvedSections = $activeSections->map(function (HomepageSection $section) use (
            $leadStory, $supportingStories, $latestNews, $trendingNews, $popularNews,
            $editorsChoice, $opinionColumns, $videoArticles, $photoArticles
        ) {
            $articles = collect();

            switch ($section->section_type) {
                case HomepageSectionType::Hero:
                    if ($leadStory) {
                        $articles->push($leadStory);
                    }
                    $articles = $articles->concat($supportingStories);
                    break;
                case HomepageSectionType::Latest:
                    $articles = $latestNews;
                    break;
                case HomepageSectionType::Trending:
                    $articles = $trendingNews;
                    break;
                case HomepageSectionType::Popular:
                    $articles = $popularNews;
                    break;
                case HomepageSectionType::EditorsChoice:
                    $articles = $editorsChoice;
                    break;
                case HomepageSectionType::Opinion:
                    $articles = $opinionColumns;
                    break;
                case HomepageSectionType::Video:
                    $articles = $videoArticles;
                    break;
                case HomepageSectionType::PhotoStory:
                    $articles = $photoArticles;
                    break;
                case HomepageSectionType::CategoryHighlight:
                    if ($section->category_id && $section->category) {
                        $articles = $section->category->articles()
                            ->published()
                            ->select($this->cardFields)
                            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                            ->latest('published_at')
                            ->limit($section->item_limit)
                            ->get();
                    }
                    break;
                case HomepageSectionType::CustomCurated:
                    $articles = $section->curatedArticles()
                        ->published()
                        ->select($this->cardFields)
                        ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                        ->limit($section->item_limit)
                        ->get();
                    break;
            }

            $section->resolvedArticles = $articles;

            return $section;
        });

        return [
            'breakingItems' => $breakingItems,
            'breakingNews' => $breakingNews,
            'leadStory' => $leadStory,
            'supportingStories' => $supportingStories,
            'latestNews' => $latestNews,
            'trendingNews' => $trendingNews,
            'popularNews' => $popularNews,
            'editorsChoice' => $editorsChoice,
            'opinionColumns' => $opinionColumns,
            'categorySections' => $categorySections,
            'videoArticles' => $videoArticles,
            'photoArticles' => $photoArticles,
            'activeSections' => $resolvedSections,
        ];
    }
}
