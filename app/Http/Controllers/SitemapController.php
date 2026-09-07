<?php

namespace App\Http\Controllers;

use App\Enums\ArticleType;
use App\Enums\PageType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use App\Services\SettingsService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Display the primary sitemap index.
     */
    public function index(): Response
    {
        $xml = Cache::remember('topnews.sitemap.index', 3600, function () {
            $latestArticle = Article::published()->latest('published_at')->first();
            $lastmod = $latestArticle?->published_at?->toIso8601String() ?? now()->toIso8601String();

            $sitemaps = [
                ['loc' => url('/sitemap-articles.xml'), 'lastmod' => $lastmod],
                ['loc' => url('/sitemap-pages.xml'), 'lastmod' => now()->toIso8601String()],
                ['loc' => url('/sitemap-categories.xml'), 'lastmod' => now()->toIso8601String()],
                ['loc' => url('/sitemap-authors.xml'), 'lastmod' => now()->toIso8601String()],
            ];

            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
            foreach ($sitemaps as $sm) {
                $out .= "  <sitemap>\n";
                $out .= '    <loc>'.e($sm['loc'])."</loc>\n";
                $out .= '    <lastmod>'.e($sm['lastmod'])."</lastmod>\n";
                $out .= "  </sitemap>\n";
            }
            $out .= '</sitemapindex>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Display the published articles sitemap.
     */
    public function articles(): Response
    {
        $xml = Cache::remember('topnews.sitemap.articles', 1800, function () {
            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";

            // Chunked query to avoid memory exhaustion on large datasets
            Article::published()
                ->select(['id', 'slug', 'title', 'published_at', 'updated_at', 'featured_image', 'featured_media_id'])
                ->with(['featuredMedia:id,path,disk'])
                ->latest('published_at')
                ->chunk(500, function ($articles) use (&$out) {
                    foreach ($articles as $article) {
                        $loc = route('news.show', $article->slug);
                        $lastmod = ($article->updated_at ?? $article->published_at)->toIso8601String();

                        $out .= "  <url>\n";
                        $out .= '    <loc>'.e($loc)."</loc>\n";
                        $out .= '    <lastmod>'.e($lastmod)."</lastmod>\n";
                        $out .= "    <changefreq>hourly</changefreq>\n";
                        $out .= "    <priority>0.8</priority>\n";

                        $imageUrl = $article->featuredMedia?->url;
                        if ($imageUrl) {
                            $out .= "    <image:image>\n";
                            $out .= '      <image:loc>'.e($imageUrl)."</image:loc>\n";
                            $out .= '      <image:title>'.e($article->title)."</image:title>\n";
                            $out .= "    </image:image>\n";
                        }

                        $out .= "  </url>\n";
                    }
                });

            $out .= '</urlset>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Display the published static institutional pages sitemap.
     */
    public function pages(): Response
    {
        $xml = Cache::remember('topnews.sitemap.pages', 3600, function () {
            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            // 1. Homepage
            $out .= "  <url>\n";
            $out .= '    <loc>'.e(url('/'))."</loc>\n";
            $out .= '    <lastmod>'.e(now()->toIso8601String())."</lastmod>\n";
            $out .= "    <changefreq>always</changefreq>\n";
            $out .= "    <priority>1.0</priority>\n";
            $out .= "  </url>\n";

            // 2. Static sections
            $sections = [
                route('latest') => 'hourly',
                route('popular.index') => 'hourly',
                route('trending.index') => 'hourly',
                route('opinion.index') => 'daily',
                route('video.index') => 'daily',
                route('photo-story.index') => 'daily',
                route('editors-choice.index') => 'daily',
                route('editorial.team') => 'weekly',
            ];

            foreach ($sections as $url => $freq) {
                $out .= "  <url>\n";
                $out .= '    <loc>'.e($url)."</loc>\n";
                $out .= '    <lastmod>'.e(now()->toIso8601String())."</lastmod>\n";
                $out .= "    <changefreq>{$freq}</changefreq>\n";
                $out .= "    <priority>0.7</priority>\n";
                $out .= "  </url>\n";
            }

            // 3. Published Pages from DB
            $pages = Page::published()->where('show_in_search', true)->get();
            foreach ($pages as $p) {
                $loc = match ($p->page_type) {
                    PageType::About => route('about'),
                    PageType::EditorialGuidelines => route('editorial.guidelines'),
                    PageType::PrivacyPolicy => route('privacy'),
                    PageType::Terms => route('terms'),
                    PageType::Disclaimer => route('disclaimer'),
                    PageType::AdvertisingInfo => route('advertise'),
                    PageType::Contact => route('contact'),
                    default => route('page.show', $p->slug),
                };

                $out .= "  <url>\n";
                $out .= '    <loc>'.e($loc)."</loc>\n";
                $out .= '    <lastmod>'.e($p->updated_at->toIso8601String())."</lastmod>\n";
                $out .= "    <changefreq>monthly</changefreq>\n";
                $out .= "    <priority>0.5</priority>\n";
                $out .= "  </url>\n";
            }

            $out .= '</urlset>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Display categories sitemap.
     */
    public function categories(): Response
    {
        $xml = Cache::remember('topnews.sitemap.categories', 3600, function () {
            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            $categories = Category::active()->whereHas('articles', function ($q) {
                $q->published();
            })->get();

            foreach ($categories as $cat) {
                $out .= "  <url>\n";
                $out .= '    <loc>'.e(route('category.show', $cat->slug))."</loc>\n";
                $out .= '    <lastmod>'.e($cat->updated_at->toIso8601String())."</lastmod>\n";
                $out .= "    <changefreq>hourly</changefreq>\n";
                $out .= "    <priority>0.7</priority>\n";
                $out .= "  </url>\n";
            }

            $out .= '</urlset>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Display author profiles sitemap.
     */
    public function authors(): Response
    {
        $xml = Cache::remember('topnews.sitemap.authors', 3600, function () {
            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            $authors = User::whereHas('articles', function ($q) {
                $q->published();
            })->get();

            foreach ($authors as $author) {
                $out .= "  <url>\n";
                $out .= '    <loc>'.e(route('author.show', $author->username))."</loc>\n";
                $out .= '    <lastmod>'.e($author->updated_at->toIso8601String())."</lastmod>\n";
                $out .= "    <changefreq>daily</changefreq>\n";
                $out .= "    <priority>0.6</priority>\n";
                $out .= "  </url>\n";
            }

            $out .= '</urlset>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Display Google News specialized sitemap (articles from last 48 hours).
     */
    public function news(): Response
    {
        $xml = Cache::remember('topnews.sitemap.news', 900, function () {
            $siteName = $this->settings->get('general.site_name', 'TopNews');
            $since = Carbon::now()->subHours(48);

            $articles = Article::published()
                ->where('published_at', '>=', $since)
                ->where('content_type', ArticleType::News->value)
                ->with(['category:id,name'])
                ->latest('published_at')
                ->limit(1000)
                ->get();

            $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">'."\n";

            foreach ($articles as $article) {
                $loc = route('news.show', $article->slug);
                $pubDate = $article->published_at->toIso8601String();

                $out .= "  <url>\n";
                $out .= '    <loc>'.e($loc)."</loc>\n";
                $out .= "    <news:news>\n";
                $out .= "      <news:publication>\n";
                $out .= '        <news:name>'.e($siteName)."</news:name>\n";
                $out .= "        <news:language>id</news:language>\n";
                $out .= "      </news:publication>\n";
                $out .= '      <news:publication_date>'.e($pubDate)."</news:publication_date>\n";
                $out .= '      <news:title>'.e($article->title)."</news:title>\n";
                $out .= "    </news:news>\n";
                $out .= "  </url>\n";
            }

            $out .= '</urlset>';

            return $out;
        });

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
