<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicPagesEmptyStateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_homepage_renders(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_latest_news_renders(): void
    {
        $this->get('/latest')->assertStatus(200);
    }

    public function test_category_renders(): void
    {
        $this->get('/category/nasional')->assertStatus(200);
    }

    public function test_search_renders(): void
    {
        $this->get('/search?q=nasional')->assertStatus(200);
    }

    public function test_opinion_renders(): void
    {
        $this->get('/opinion')->assertStatus(200);
    }

    public function test_popular_renders(): void
    {
        $this->get('/popular')->assertStatus(200);
    }

    public function test_trending_renders(): void
    {
        $this->get('/trending')->assertStatus(200);
    }

    public function test_video_discovery_renders(): void
    {
        $this->get('/video')->assertStatus(200);
    }

    public function test_photo_story_renders(): void
    {
        $this->get('/photo-story')->assertStatus(200);
    }

    public function test_static_about_renders(): void
    {
        $this->get('/about')->assertStatus(200);
    }

    public function test_static_contact_renders(): void
    {
        $this->get('/contact')->assertStatus(200);
    }

    public function test_sitemap_index_renders(): void
    {
        $this->get('/sitemap.xml')->assertStatus(200);
    }

    public function test_sitemap_articles_renders(): void
    {
        $this->get('/sitemap-articles.xml')->assertStatus(200);
    }

    public function test_sitemap_pages_renders(): void
    {
        $this->get('/sitemap-pages.xml')->assertStatus(200);
    }

    public function test_sitemap_categories_renders(): void
    {
        $this->get('/sitemap-categories.xml')->assertStatus(200);
    }

    public function test_sitemap_authors_renders(): void
    {
        $this->get('/sitemap-authors.xml')->assertStatus(200);
    }

    public function test_news_sitemap_renders(): void
    {
        $this->get('/news-sitemap.xml')->assertStatus(200);
    }

    public function test_robots_txt_renders(): void
    {
        $this->get('/robots.txt')->assertStatus(200);
    }
}
