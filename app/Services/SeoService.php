<?php

namespace App\Services;

use App\Enums\ArticleType;
use App\Models\Article;
use App\Models\Category;
use App\Models\Media;
use App\Models\Page;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;

class SeoService
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    public function getSiteName(): string
    {
        return $this->settings->get('general.site_name', config('topnews.name', 'TopNews'));
    }

    public function getSiteTagline(): string
    {
        return $this->settings->get('general.tagline', 'Informasi Cepat. Perspektif Jelas. Berita Terpercaya.');
    }

    public function getDefaultDescription(): string
    {
        return $this->settings->get('seo.default_meta_description', $this->settings->get('general.site_description', 'Portal berita independen terlengkap di Indonesia.'));
    }

    public function getDefaultSocialImageUrl(): ?string
    {
        $mediaId = $this->settings->get('seo.default_social_image_id') ?: $this->settings->get('branding.logo_media_id');
        if ($mediaId) {
            $media = Media::find($mediaId);
            if ($media && $media->url) {
                return $media->url;
            }
        }

        return null;
    }

    public function getOrganizationSchema(): array
    {
        $siteName = $this->getSiteName();
        $socials = $this->settings->getGroup('social');
        $sameAs = array_values(array_filter([
            $socials['facebook'] ?? null,
            $socials['x'] ?? null,
            $socials['instagram'] ?? null,
            $socials['youtube'] ?? null,
            $socials['tiktok'] ?? null,
            $socials['linkedin'] ?? null,
        ]));

        $schema = [
            '@type' => 'NewsMediaOrganization',
            '@id' => url('/#organization'),
            'name' => $this->settings->get('seo.organization_name', $siteName),
            'url' => url('/'),
        ];

        $logoUrl = $this->getDefaultSocialImageUrl();
        if ($logoUrl) {
            $schema['logo'] = [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ];
        }

        if (! empty($sameAs)) {
            $schema['sameAs'] = $sameAs;
        }

        return $schema;
    }

    public function getWebsiteSchema(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/#website'),
            'url' => url('/'),
            'name' => $this->getSiteName(),
            'description' => $this->getDefaultDescription(),
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => url('/#organization')],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/search').'?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * SEO for Homepage.
     */
    public function forHome(): SeoData
    {
        $siteName = $this->getSiteName();
        $tagline = $this->getSiteTagline();
        $title = $this->settings->get('seo.default_meta_title', "{$siteName} — {$tagline}");
        $description = $this->getDefaultDescription();
        $canonical = url('/');
        $imageUrl = $this->getDefaultSocialImageUrl();

        $schemas = [
            $this->getOrganizationSchema(),
            $this->getWebsiteSchema(),
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: 'index,follow',
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $title,
                'og:description' => $description,
                'og:type' => 'website',
                'og:url' => $canonical,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => $imageUrl ? 'summary_large_image' : 'summary',
                'twitter:title' => $title,
                'twitter:description' => $description,
                'twitter:image' => $imageUrl,
            ],
            schemas: $schemas
        );
    }

    /**
     * SEO for Single Article.
     */
    public function forArticle(Article $article): SeoData
    {
        $siteName = $this->getSiteName();

        // 1. Title Hierarchy
        $title = $article->seo_title ?: "{$article->title} — {$siteName}";

        // 2. Description Hierarchy (strip tags)
        if (! empty($article->seo_description)) {
            $description = trim(strip_tags($article->seo_description));
        } elseif (! empty($article->excerpt)) {
            $description = trim(strip_tags($article->excerpt));
        } else {
            $description = trim(strip_tags(Str::limit($article->content, 160)));
        }

        // 3. Canonical URL
        $canonical = $article->canonical_url ?: route('news.show', $article->slug);

        // 4. Robots
        $robots = ($article->isPublished() && $article->robots_index) ? 'index,follow' : 'noindex,nofollow';

        // 5. Image
        $imageUrl = $article->featuredMedia?->url ?: $this->getDefaultSocialImageUrl();

        // 6. Open Graph & Twitter
        $categoryName = $article->category ? $article->category->name : 'Berita';
        $authorName = $article->author ? $article->author->name : $siteName;

        $openGraph = [
            'og:site_name' => $siteName,
            'og:title' => $title,
            'og:description' => $description,
            'og:type' => 'article',
            'og:url' => $canonical,
            'og:image' => $imageUrl,
        ];

        $twitter = [
            'twitter:card' => $imageUrl ? 'summary_large_image' : 'summary',
            'twitter:title' => $title,
            'twitter:description' => $description,
            'twitter:image' => $imageUrl,
        ];

        $articleMeta = [
            'article:published_time' => $article->published_at?->toIso8601String(),
            'article:modified_time' => $article->updated_at?->toIso8601String(),
            'article:author' => $authorName,
            'article:section' => $categoryName,
        ];

        if ($article->relationLoaded('tags') && $article->tags->isNotEmpty()) {
            $articleMeta['article:tag'] = $article->tags->pluck('name')->all();
        }

        // 7. Structured Data JSON-LD
        $schemas = [];

        // BreadcrumbList
        $breadcrumbs = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => url('/'),
                ],
            ],
        ];

        if ($article->category) {
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $article->category->name,
                'item' => route('category.show', $article->category->slug),
            ];
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $article->title,
                'item' => $canonical,
            ];
        } else {
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => $article->title,
                'item' => $canonical,
            ];
        }
        $schemas[] = $breadcrumbs;

        // Article Schema
        $articleSchemaType = match ($article->content_type) {
            ArticleType::News, ArticleType::News->value => 'NewsArticle',
            ArticleType::Video, ArticleType::Video->value => 'NewsArticle',
            ArticleType::PhotoStory, ArticleType::PhotoStory->value => 'Article',
            default => 'Article',
        };

        $articleSchema = [
            '@type' => $articleSchemaType,
            '@id' => $canonical.'#article',
            'isPartOf' => ['@id' => url('/#website')],
            'headline' => $article->title,
            'description' => $description,
            'datePublished' => $article->published_at?->toIso8601String() ?? now()->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String() ?? now()->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
            'inLanguage' => 'id-ID',
            'articleSection' => $categoryName,
            'publisher' => $this->getOrganizationSchema(),
        ];

        if ($article->author) {
            $articleSchema['author'] = [
                '@type' => 'Person',
                'name' => $article->author->name,
                'url' => route('author.show', $article->author->username),
            ];
        }

        if ($imageUrl) {
            $articleSchema['image'] = [
                '@type' => 'ImageObject',
                'url' => $imageUrl,
            ];
        }

        $schemas[] = $articleSchema;

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: $robots,
            openGraph: $openGraph,
            twitter: $twitter,
            articleMeta: $articleMeta,
            schemas: $schemas
        );
    }

    /**
     * SEO for Category Archives.
     */
    public function forCategory(Category $category, int $page = 1): SeoData
    {
        $siteName = $this->getSiteName();
        $title = $category->name." — Berita & Liputan {$siteName}";
        if ($page > 1) {
            $title .= " (Halaman {$page})";
        }

        $description = $category->description
            ? trim(strip_tags($category->description))
            : "Kumpulan berita dan artikel liputan {$category->name} terkini, akurat, dan terpercaya di {$siteName}.";

        $canonical = $page > 1
            ? route('category.show', $category->slug).'?page='.$page
            : route('category.show', $category->slug);

        $imageUrl = $this->getDefaultSocialImageUrl();

        $breadcrumbs = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $category->name,
                    'item' => route('category.show', $category->slug),
                ],
            ],
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: 'index,follow',
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $title,
                'og:description' => $description,
                'og:type' => 'website',
                'og:url' => $canonical,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $title,
                'twitter:description' => $description,
                'twitter:image' => $imageUrl,
            ],
            schemas: [$breadcrumbs]
        );
    }

    /**
     * SEO for Tag Archives.
     */
    public function forTag(Tag $tag, int $page = 1): SeoData
    {
        $siteName = $this->getSiteName();
        $title = "Berita #{$tag->name} Terkini — {$siteName}";
        if ($page > 1) {
            $title .= " (Halaman {$page})";
        }

        $description = "Kumpulan berita dan topik terkini seputar #{$tag->name} di portal berita {$siteName}.";
        $canonical = $page > 1
            ? route('tag.show', $tag->slug).'?page='.$page
            : route('tag.show', $tag->slug);

        $imageUrl = $this->getDefaultSocialImageUrl();

        $breadcrumbs = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => '#'.$tag->name,
                    'item' => route('tag.show', $tag->slug),
                ],
            ],
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: 'index,follow',
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $title,
                'og:description' => $description,
                'og:type' => 'website',
                'og:url' => $canonical,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => 'summary',
                'twitter:title' => $title,
                'twitter:description' => $description,
            ],
            schemas: [$breadcrumbs]
        );
    }

    /**
     * SEO for Author Profiles.
     */
    public function forAuthor(User $author, int $page = 1): SeoData
    {
        $siteName = $this->getSiteName();
        $title = "{$author->name} — Profil Jurnalis & Redaksi {$siteName}";
        if ($page > 1) {
            $title .= " (Halaman {$page})";
        }

        $description = $author->bio
            ? trim(strip_tags(Str::limit($author->bio, 160)))
            : "Profil dan arsip artikel liputan berita jurnalis {$author->name} di portal berita {$siteName}.";

        $canonical = $page > 1
            ? route('author.show', $author->username).'?page='.$page
            : route('author.show', $author->username);

        $imageUrl = $author->avatar_url ?: $this->getDefaultSocialImageUrl();

        $breadcrumbs = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Dewan Redaksi',
                    'item' => route('editorial.team'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $author->name,
                    'item' => route('author.show', $author->username),
                ],
            ],
        ];

        $personSchema = [
            '@type' => 'Person',
            'name' => $author->name,
            'url' => route('author.show', $author->username),
            'jobTitle' => $author->public_title ?: 'Jurnalis',
            'worksFor' => $this->getOrganizationSchema(),
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: 'index,follow',
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $title,
                'og:description' => $description,
                'og:type' => 'profile',
                'og:url' => $canonical,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => 'summary',
                'twitter:title' => $title,
                'twitter:description' => $description,
                'twitter:image' => $imageUrl,
            ],
            schemas: [$breadcrumbs, $personSchema]
        );
    }

    /**
     * SEO for Static Institutional Pages.
     */
    public function forPage(Page $page): SeoData
    {
        $siteName = $this->getSiteName();
        $title = $page->seo_title ?: "{$page->title} — {$siteName}";

        if (! empty($page->seo_description)) {
            $description = trim(strip_tags($page->seo_description));
        } elseif (! empty($page->excerpt)) {
            $description = trim(strip_tags($page->excerpt));
        } else {
            $description = trim(strip_tags(Str::limit($page->content, 160)));
        }

        $canonical = route('page.show', $page->slug);
        $imageUrl = $this->getDefaultSocialImageUrl();
        $robots = ($page->isPublished() && $page->show_in_search) ? 'index,follow' : 'noindex,nofollow';

        $breadcrumbs = [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $page->title,
                    'item' => $canonical,
                ],
            ],
        ];

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: $robots,
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $title,
                'og:description' => $description,
                'og:type' => 'website',
                'og:url' => $canonical,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => 'summary',
                'twitter:title' => $title,
                'twitter:description' => $description,
            ],
            schemas: [$breadcrumbs]
        );
    }

    /**
     * SEO for Search Results.
     */
    public function forSearch(string $query, int $page = 1): SeoData
    {
        $siteName = $this->getSiteName();
        $title = $query ? "Pencarian: \"{$query}\" — {$siteName}" : "Pencarian Berita — {$siteName}";
        if ($page > 1) {
            $title .= " (Halaman {$page})";
        }

        $description = "Temukan berita, opini, foto, dan video terkini di {$siteName}.";
        $canonical = route('search', array_filter(['q' => $query, 'page' => $page > 1 ? $page : null]));

        return new SeoData(
            title: $title,
            description: $description,
            canonicalUrl: $canonical,
            robots: 'noindex,follow' // Search results should strictly not be indexed
        );
    }

    /**
     * SEO for General Section (e.g. Latest, Popular, Trending, Video, Photo Story).
     */
    public function forSection(string $title, string $description, string $canonicalUrl, string $robots = 'index,follow'): SeoData
    {
        $siteName = $this->getSiteName();
        $fullTitle = "{$title} — {$siteName}";
        $imageUrl = $this->getDefaultSocialImageUrl();

        return new SeoData(
            title: $fullTitle,
            description: $description,
            canonicalUrl: $canonicalUrl,
            robots: $robots,
            openGraph: [
                'og:site_name' => $siteName,
                'og:title' => $fullTitle,
                'og:description' => $description,
                'og:type' => 'website',
                'og:url' => $canonicalUrl,
                'og:image' => $imageUrl,
            ],
            twitter: [
                'twitter:card' => 'summary_large_image',
                'twitter:title' => $fullTitle,
                'twitter:description' => $description,
                'twitter:image' => $imageUrl,
            ]
        );
    }
}
