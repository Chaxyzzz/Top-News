<?php

namespace Tests\Feature;

use App\Services\ContentSanitizerService;
use Tests\TestCase;

class ContentSanitizerTest extends TestCase
{
    public function test_content_sanitizer_removes_dangerous_tags_and_scripts(): void
    {
        $malicious = '<p>Paragraf normal</p><script>alert("XSS")</script><h2>Subjudul</h2><iframe src="https://evil.com"></iframe>';
        $cleaned = ContentSanitizerService::sanitize($malicious);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('alert("XSS")', $cleaned);
        $this->assertStringNotContainsString('<iframe', $cleaned);
        $this->assertStringContainsString('<p>Paragraf normal</p>', $cleaned);
        $this->assertStringContainsString('<h2>Subjudul</h2>', $cleaned);
    }

    public function test_content_sanitizer_removes_inline_event_handlers(): void
    {
        $malicious = '<p onclick="stealCookies()">Teks</p><img src="x" onerror="alert(1)">';
        $cleaned = ContentSanitizerService::sanitize($malicious);

        $this->assertStringNotContainsString('onclick', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
    }

    public function test_content_sanitizer_neutralizes_javascript_links(): void
    {
        $malicious = '<a href="javascript:alert(1)">Klik Tautan Bahaya</a>';
        $cleaned = ContentSanitizerService::sanitize($malicious);

        $this->assertStringNotContainsString('javascript:', $cleaned);
    }
}
