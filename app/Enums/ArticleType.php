<?php

namespace App\Enums;

enum ArticleType: string
{
    case News = 'news';
    case Feature = 'feature';
    case Opinion = 'opinion';
    case Editorial = 'editorial';
    case Analysis = 'analysis';
    case Interview = 'interview';
    case Video = 'video';
    case PhotoStory = 'photo_story';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::News => 'Berita',
            self::Feature => 'Feature',
            self::Opinion => 'Opini / Kolom',
            self::Editorial => 'Tajuk Rencana',
            self::Analysis => 'Analisis Mendalam',
            self::Interview => 'Wawancara Eksklusif',
            self::Video => 'Video Berita',
            self::PhotoStory => 'Kisah Foto',
        };
    }
}
