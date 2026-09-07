<?php

namespace App\Http\Controllers;

use App\Enums\ArticleType;
use App\Models\Article;
use App\Services\DemoNewsService;
use App\Services\SeoService;
use App\Services\TrendingArticleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MediaDiscoveryController extends Controller
{
    public function __construct(
        protected TrendingArticleService $trendingService,
        protected DemoNewsService $newsService
    ) {}

    /**
     * Common fields required for public video/photo cards.
     *
     * @var list<string>
     */
    protected array $cardFields = [
        'id', 'uuid', 'title', 'slug', 'subtitle', 'excerpt', 'content_type',
        'category_id', 'author_id', 'featured_media_id', 'featured_image', 'featured_image_caption', 'featured_image_alt',
        'is_featured', 'is_breaking', 'is_editor_choice', 'is_sponsored',
        'published_at', 'reading_time', 'views_count',
    ];

    /**
     * Display public Video news portal.
     */
    public function video(Request $request): View
    {
        $articles = Article::published()
            ->where('content_type', ArticleType::Video->value)
            ->select($this->cardFields)
            ->with([
                'category:id,name,slug,accent_color',
                'author:id,name,username,avatar',
                'featuredMedia',
                'video.thumbnailMedia',
            ])
            ->latest('published_at')
            ->paginate(12);

        $page = (int) $request->query('page', 1);
        $canonical = $page > 1 ? route('video.index').'?page='.$page : route('video.index');

        return view('pages.video', [
            'articles' => $articles,
            'trending' => $this->trendingService->getTrending(5),
            'breaking' => $this->newsService->getBreakingNews(),
            'seoData' => app(SeoService::class)->forSection('Video Berita', 'Liputan video jurnalistik eksklusif dan wawancara mendalam TopNews.', $canonical),
        ]);
    }

    /**
     * Display public Photo Story portal.
     */
    public function photoStory(Request $request): View
    {
        $articles = Article::published()
            ->where('content_type', ArticleType::PhotoStory->value)
            ->select($this->cardFields)
            ->with([
                'category:id,name,slug,accent_color',
                'author:id,name,username,avatar',
                'featuredMedia',
                'photoStory.gallery.coverMedia',
                'photoStory.gallery.media',
            ])
            ->latest('published_at')
            ->paginate(12);

        $page = (int) $request->query('page', 1);
        $canonical = $page > 1 ? route('photo-story.index').'?page='.$page : route('photo-story.index');

        return view('pages.photo-story', [
            'articles' => $articles,
            'trending' => $this->trendingService->getTrending(5),
            'breaking' => $this->newsService->getBreakingNews(),
            'seoData' => app(SeoService::class)->forSection('Foto Cerita & Galeri', 'Galeri esai visual dan fotografi jurnalistik TopNews.', $canonical),
        ]);
    }
}
