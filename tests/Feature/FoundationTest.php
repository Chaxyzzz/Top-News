<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test that the homepage loads successfully with 200 OK and contains key editorial landmarks.
     */
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('TOP');
        $response->assertSee('NEWS');
        $response->assertSee('Berita Terbaru');
    }

    /**
     * Test that the health-check route returns database connectivity OK.
     */
    public function test_health_check_endpoint_returns_ok(): void
    {
        $response = $this->get(route('health-check'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'application' => config('topnews.name'),
            'database' => 'OK',
        ]);
    }

    /**
     * Test that all primary navigation routes are accessible.
     */
    public function test_primary_navigation_routes_are_accessible(): void
    {
        $this->get(route('latest'))->assertStatus(200)->assertSee('Berita Terbaru');
        $this->get(route('category.show', ['slug' => 'nasional']))->assertStatus(200)->assertSee('Nasional');
        $this->get(route('about'))->assertStatus(200)->assertSee('Tentang Kami');
        $this->get(route('contact'))->assertStatus(200)->assertSee('Kontak');
    }

    /**
     * Test that search results page handles empty and query states correctly.
     */
    public function test_search_page_handles_queries_and_empty_states(): void
    {
        // Empty state
        $emptyResponse = $this->get(route('search'));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('Pusat Pencarian Berita');

        // Query state
        $queryResponse = $this->get(route('search', ['q' => 'Teknologi']));
        $queryResponse->assertStatus(200);
        $queryResponse->assertSee('Hasil pencarian untuk:');
        $queryResponse->assertSee('Teknologi');
    }

    /**
     * Test prototype article reading page.
     */
    public function test_article_detail_prototype_page_loads_with_metadata(): void
    {
        $response = $this->get(route('news.show', ['slug' => 'peta-jalan-indonesia-digital-2030-transformasi-sektor-publik']));

        $response->assertStatus(200);
        $response->assertSee('Peta Jalan Indonesia Digital 2030');
        $response->assertSee('Bagikan:');
        $response->assertSee('Berita Terkait');
    }

    /**
     * Test safe contact form submission.
     */
    public function test_contact_form_submits_safely(): void
    {
        $response = $this->post(route('contact.submit'), [
            'name' => 'Ahmad Pembaca',
            'email' => 'ahmad@example.com',
            'subject' => 'Apresiasi Liputan TopNews',
            'message' => 'Liputan jurnalisme TopNews sangat mendalam dan berimbang.',
        ]);

        $response->assertRedirect(route('contact'));
        $response->assertSessionHas('success');
    }
}
