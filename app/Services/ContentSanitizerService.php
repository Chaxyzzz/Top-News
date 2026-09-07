<?php

namespace App\Services;

class ContentSanitizerService
{
    /**
     * Allowed HTML tags.
     */
    protected static array $allowedTags = [
        '<p>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>',
        '<strong>', '<b>', '<em>', '<i>', '<u>', '<s>', '<strike>',
        '<a>', '<blockquote>', '<ul>', '<ol>', '<li>',
        '<figure>', '<figcaption>', '<img>', '<hr>', '<br>', '<span>', '<div>',
    ];

    /**
     * Sanitize article HTML content.
     */
    public static function sanitize(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Remove dangerous blocks and their contents completely FIRST
        $cleaned = preg_replace('#<script\b[^>]*>(.*?)</script>#is', '', $html);
        $cleaned = preg_replace('#<iframe\b[^>]*>(.*?)</iframe>#is', '', $cleaned);
        $cleaned = preg_replace('#<style\b[^>]*>(.*?)</style>#is', '', $cleaned);

        // 2. Strip all tags except allowed tags
        $cleaned = strip_tags($cleaned, implode('', self::$allowedTags));

        // 3. Remove inline javascript event handlers (e.g., onclick, onerror, onload)
        $cleaned = preg_replace('/\son\w+\s*=\s*(?:["\'][^"\']*["\']|[^\s>]+)/i', '', $cleaned);

        // 4. Remove javascript: protocol in href or src
        $cleaned = preg_replace('/(href|src)\s*=\s*["\']\s*javascript:[^"\']*["\']/i', '$1="#"', $cleaned);

        // 5. Ensure all external <a> tags have rel="noopener noreferrer"
        $cleaned = preg_replace_callback('/<a\s+([^>]+)>/i', function ($matches) {
            $attrs = $matches[1];
            if (! str_contains($attrs, 'rel=')) {
                $attrs .= ' rel="noopener noreferrer"';
            }

            return "<a {$attrs}>";
        }, $cleaned);

        return trim($cleaned);
    }
}
