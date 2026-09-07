<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Services\ArticleViewService;
use App\Services\ContentSanitizerService;
use App\Services\DemoNewsService;
use App\Services\PopularArticleService;
use App\Services\RelatedArticleService;
use App\Services\SeoService;
use App\Services\TrendingArticleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        protected DemoNewsService $newsService,
        protected ArticleViewService $viewService,
        protected PopularArticleService $popularService,
        protected TrendingArticleService $trendingService,
        protected RelatedArticleService $relatedService,
        protected SeoService $seoService
    ) {}

    /**
     * Common fields required for public cards.
     *
     * @var list<string>
     */
    protected array $cardFields = [
        'id', 'uuid', 'title', 'slug', 'subtitle', 'excerpt', 'content_type',
        'category_id', 'author_id', 'featured_image', 'featured_image_caption', 'featured_image_alt',
        'is_featured', 'is_breaking', 'is_editor_choice', 'is_sponsored',
        'published_at', 'reading_time', 'views_count',
    ];

    /**
     * Display the latest news feed page.
     */
    public function latest(Request $request): View
    {
        $articles = Article::published()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->latest('published_at')
            ->paginate(15);

        $page = (int) $request->query('page', 1);
        $canonical = $page > 1 ? route('latest').'?page='.$page : route('latest');

        return view('pages.latest', [
            'articles' => $articles,
            'trending' => $this->trendingService->getTrending(5),
            'breaking' => $this->newsService->getBreakingNews(),
            'seoData' => $this->seoService->forSection('Berita Terkini', 'Kumpulan berita dan liputan investigasi terbaru hari ini di TopNews.', $canonical),
        ]);
    }

    /**
     * Display the category page.
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->first();

        if ($category) {
            $articles = $category->articles()
                ->published()
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                ->latest('published_at')
                ->paginate(12);

            $featured = $category->articles()
                ->published()
                ->select($this->cardFields)
                ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
                ->orderByDesc('homepage_priority')
                ->orderByDesc('is_featured')
                ->latest('published_at')
                ->first();

            $categoryData = [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description ?? 'Kumpulan berita terkini dan ulasan mendalam terkait '.$category->name.'.',
            ];

            return view('pages.category', [
                'category' => $categoryData,
                'articles' => $articles,
                'featured' => $featured,
                'trending' => $this->trendingService->getTrending(5),
                'breaking' => $this->newsService->getBreakingNews(),
                'seoData' => $this->seoService->forCategory($category, (int) request('page', 1)),
            ]);
        }

        // Demo fallback if category not yet created
        $categories = $this->newsService->getCategories();
        $demoCategory = $categories[$slug] ?? [
            'name' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'description' => 'Kumpulan berita terkini dan ulasan mendalam terkait '.ucwords(str_replace('-', ' ', $slug)).'.',
        ];

        return view('pages.category', [
            'category' => $demoCategory,
            'articles' => $this->newsService->getLatestNews(9),
            'featured' => $this->newsService->getLeadStory(),
            'trending' => $this->newsService->getTrendingNews(),
            'breaking' => $this->newsService->getBreakingNews(),
        ]);
    }

    /**
     * Display the article reading page and record view event.
     */
    public function show(string $slug): View
    {
        // 1. Check if genuinely published in database
        $article = Article::published()
            ->with([
                'category:id,name,slug,accent_color',
                'author:id,name,username,avatar',
                'editor:id,name,username',
                'tags:id,name,slug',
                'featuredMedia',
                'video.thumbnailMedia',
                'photoStory.gallery.media',
                'photoStory.gallery.photographer',
            ])
            ->where('slug', $slug)
            ->first();

        if ($article) {
            // Record view with session deduplication
            $this->viewService->recordView($article);

            $sanitizedContent = ContentSanitizerService::sanitize($article->content);
            $related = $this->relatedService->getRelatedArticles($article, 3);
            $recommended = $this->relatedService->getRecommendedArticles($article, $related->pluck('id')->all(), 3);
            $navigation = $this->relatedService->getPreviousAndNext($article);

            return view('pages.article', [
                'article' => $article,
                'sanitizedContent' => $sanitizedContent,
                'related' => $related,
                'recommended' => $recommended,
                'previousArticle' => $navigation['previous'],
                'nextArticle' => $navigation['next'],
                'trending' => $this->trendingService->getTrending(5),
                'breaking' => $this->newsService->getBreakingNews(),
                'seoData' => $this->seoService->forArticle($article),
            ]);
        }

        // 2. Drafts, scheduled, archived, or nonexistent articles return 404
        abort(404);
    }

    /**
     * Display public opinion archive page.
     */
    public function opinion(Request $request): View
    {
        $articles = Article::published()
            ->opinion()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->latest('published_at')
            ->paginate(12);

        $page = (int) $request->query('page', 1);
        $canonical = $page > 1 ? route('opinion.index').'?page='.$page : route('opinion.index');

        return view('pages.opinion', [
            'articles' => $articles,
            'trending' => $this->trendingService->getTrending(5),
            'seoData' => $this->seoService->forSection('Kolom & Opini', 'Perspektif, analisis, dan kolom mendalam dari para jurnalis dan akademisi independen.', $canonical),
        ]);
    }

    /**
     * Display public popular / most read archive page.
     */
    public function popular(Request $request): View
    {
        $period = $request->query('period', '7days');
        if (! in_array($period, ['today', '7days', '30days'])) {
            $period = '7days';
        }

        $articles = $this->popularService->getPopular($period, 15);

        return view('pages.popular', [
            'articles' => $articles,
            'currentPeriod' => $period,
            'trending' => $this->trendingService->getTrending(5),
            'seoData' => $this->seoService->forSection('Berita Terpopuler', 'Berita dan liputan yang paling banyak dibaca publik di TopNews.', route('popular.index')),
        ]);
    }

    /**
     * Display public trending archive page.
     */
    public function trending(Request $request): View
    {
        $articles = $this->trendingService->getTrending(15);

        return view('pages.trending', [
            'articles' => $articles,
            'seoData' => $this->seoService->forSection('Topik Trending', 'Isu terkini dan perbincangan hangat yang sedang tren di ruang publik.', route('trending.index')),
        ]);
    }

    /**
     * Display public editor's choice archive page.
     */
    public function editorsChoice(Request $request): View
    {
        $articles = Article::published()
            ->editorChoice()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->latest('published_at')
            ->paginate(12);

        $page = (int) $request->query('page', 1);
        $canonical = $page > 1 ? route('editors-choice.index').'?page='.$page : route('editors-choice.index');

        return view('pages.editors-choice', [
            'articles' => $articles,
            'trending' => $this->trendingService->getTrending(5),
            'seoData' => $this->seoService->forSection('Pilihan Redaksi', 'Kurasi berita dan artikel terbaik pilihan dewan redaksi TopNews.', $canonical),
        ]);
    }

    /**
     * Display public author page.
     */
    public function author(string $username): View
    {
        $author = User::where('username', $username)->firstOrFail();

        $articles = $author->articles()
            ->published()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color'])
            ->latest('published_at')
            ->paginate(12);

        $seoData = $this->seoService->forAuthor($author, (int) request('page', 1));

        return view('pages.author', compact('author', 'articles', 'seoData'));
    }

    /**
     * Display public tag page.
     */
    public function tag(string $slug): View
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();

        $articles = $tag->articles()
            ->published()
            ->select($this->cardFields)
            ->with(['category:id,name,slug,accent_color', 'author:id,name,username,avatar'])
            ->latest('published_at')
            ->paginate(12);

        $seoData = $this->seoService->forTag($tag, (int) request('page', 1));

        return view('pages.tag', compact('tag', 'articles', 'seoData'));
    }
}
