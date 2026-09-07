<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_response_contains_strict_security_headers(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        // 1. MIME-sniffing prevention
        $response->assertHeader('X-Content-Type-Options', 'nosniff');

        // 2. Clickjacking protection
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        // 3. Referrer Policy
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Permissions Policy
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 5. Content Security Policy
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://fonts.bunny.net', $csp);
        $this->assertStringContainsString('https://www.youtube.com', $csp);
    }

    public function test_hsts_is_not_emitted_over_insecure_http(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $this->assertFalse($response->headers->has('Strict-Transport-Security'));
    }

    public function test_hsts_is_emitted_in_production_over_https(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $response = $this->get('https://topnews.test/');
        $response->assertOk();
        $this->assertTrue($response->headers->has('Strict-Transport-Security'));
        $this->assertEquals('max-age=31536000; includeSubDomains', $response->headers->get('Strict-Transport-Security'));
    }
}
