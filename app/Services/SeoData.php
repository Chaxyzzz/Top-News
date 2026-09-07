<?php

namespace App\Services;

class SeoData
{
    /**
     * @param  array<string, mixed>  $openGraph
     * @param  array<string, mixed>  $twitter
     * @param  array<string, mixed>  $articleMeta
     * @param  array<int, array<string, mixed>>  $schemas
     */
    public function __construct(
        public string $title,
        public string $description,
        public string $canonicalUrl,
        public string $robots = 'index,follow',
        public array $openGraph = [],
        public array $twitter = [],
        public array $articleMeta = [],
        public array $schemas = [],
    ) {}

    /**
     * Get JSON-LD string safely encoded.
     */
    public function renderJsonLd(): string
    {
        if (empty($this->schemas)) {
            return '';
        }

        if (count($this->schemas) === 1) {
            $payload = $this->schemas[0];
        } else {
            $payload = [
                '@context' => 'https://schema.org',
                '@graph' => $this->schemas,
            ];
        }

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '';
    }
}
