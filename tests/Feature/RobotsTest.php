<?php

namespace Tests\Feature;

use Tests\TestCase;

class RobotsTest extends TestCase
{
    public function test_robots_txt_disallows_all_crawlers_in_non_production(): void
    {
        $response = $this->get('/robots.txt');
        $response->assertOk();
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        $this->assertStringContainsString("User-agent: *\nDisallow: /", $response->getContent());
    }

    public function test_robots_txt_serves_production_rules_and_sitemaps_in_production(): void
    {
        // Simulate production environment
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('/robots.txt');
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('Disallow: /admin/', $content);
        $this->assertStringContainsString('Disallow: /account/', $content);
        $this->assertStringContainsString('Disallow: /*preview*', $content);
        $this->assertStringContainsString('Sitemap: '.url('/sitemap.xml'), $content);
        $this->assertStringContainsString('Sitemap: '.url('/news-sitemap.xml'), $content);
    }
}
