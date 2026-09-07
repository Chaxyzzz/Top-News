<?php

namespace App\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Document = 'document';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Image => 'Gambar',
            self::Video => 'Video',
            self::Document => 'Dokumen',
        };
    }
}
