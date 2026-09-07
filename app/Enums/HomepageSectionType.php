<?php

namespace App\Enums;

enum HomepageSectionType: string
{
    case Hero = 'hero';
    case Latest = 'latest';
    case TopStories = 'top_stories';
    case Trending = 'trending';
    case Popular = 'popular';
    case EditorsChoice = 'editors_choice';
    case Opinion = 'opinion';
    case CategoryHighlight = 'category_highlight';
    case Video = 'video';
    case PhotoStory = 'photo_story';
    case CustomCurated = 'custom_curated';

    /**
     * Human-friendly Indonesian label for the CMS interface.
     */
    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Berita Utama (Hero)',
            self::Latest => 'Berita Terbaru',
            self::TopStories => 'Sorotan Utama (Top Stories)',
            self::Trending => 'Sedang Tren (Trending)',
            self::Popular => 'Terpopuler (Most Read)',
            self::EditorsChoice => 'Pilihan Editor',
            self::Opinion => 'Opini & Kolom Redaksi',
            self::CategoryHighlight => 'Sorotan Rubrik / Kategori',
            self::Video => 'Berita Video',
            self::PhotoStory => 'Galeri Foto Cerita',
            self::CustomCurated => 'Kurasi Khusus Redaksi',
        };
    }

    /**
     * Whitelist of allowed layout variants for this section type.
     *
     * @return list<HomepageLayoutVariant>
     */
    public function allowedLayouts(): array
    {
        return match ($this) {
            self::Hero => [HomepageLayoutVariant::HeroPrimary],
            self::Latest => [HomepageLayoutVariant::Grid4, HomepageLayoutVariant::LeadPlusList],
            self::TopStories => [HomepageLayoutVariant::LeadPlusList, HomepageLayoutVariant::Grid3, HomepageLayoutVariant::Grid4],
            self::Trending, self::Popular => [HomepageLayoutVariant::NumberedList, HomepageLayoutVariant::HorizontalList],
            self::EditorsChoice => [HomepageLayoutVariant::Grid3, HomepageLayoutVariant::Grid4],
            self::Opinion => [HomepageLayoutVariant::OpinionCards],
            self::CategoryHighlight => [HomepageLayoutVariant::Grid4, HomepageLayoutVariant::LeadPlusList, HomepageLayoutVariant::Grid3],
            self::Video, self::PhotoStory => [HomepageLayoutVariant::MediaGrid, HomepageLayoutVariant::Grid3],
            self::CustomCurated => [HomepageLayoutVariant::Grid4, HomepageLayoutVariant::Grid3, HomepageLayoutVariant::HorizontalList],
        };
    }

    /**
     * Whether this section type is a core system section that cannot be deleted.
     */
    public function isProtected(): bool
    {
        return in_array($this, [self::Hero, self::Latest], true);
    }
}
