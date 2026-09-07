<?php

namespace App\Services;

class VideoNewsService
{
    /**
     * Parse and extract a clean YouTube Video ID from various URL formats.
     */
    public function extractYouTubeId(string $input): ?string
    {
        $input = trim($input);

        // 1. Raw 11-character YouTube ID
        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
            return $input;
        }

        // 2. Standard YouTube URL patterns
        // e.g. https://www.youtube.com/watch?v=dQw4w9WgXcQ
        // e.g. https://youtu.be/dQw4w9WgXcQ
        // e.g. https://www.youtube.com/embed/dQw4w9WgXcQ
        // e.g. https://www.youtube.com/shorts/dQw4w9WgXcQ
        $pattern = '%^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com/(?:embed/|v/|watch\?v=|watch\?.+&v=|shorts/))([\w-]{11})(?:\S+)?$%x';

        if (preg_match($pattern, $input, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Generate privacy-enhanced YouTube embed URL.
     */
    public function generateEmbedUrl(string $videoId): string
    {
        return "https://www.youtube-nocookie.com/embed/{$videoId}?rel=0&modestbranding=1";
    }

    /**
     * Validate if an input is a legitimate YouTube URL or Video ID.
     */
    public function isValidYouTubeSource(string $input): bool
    {
        return $this->extractYouTubeId($input) !== null;
    }
}
