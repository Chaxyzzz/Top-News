<?php

namespace App\Enums;

enum HomepageLayoutVariant: string
{
    case HeroPrimary = 'hero_primary';
    case Grid4 = 'grid_4';
    case Grid3 = 'grid_3';
    case LeadPlusList = 'lead_plus_list';
    case HorizontalList = 'horizontal_list';
    case NumberedList = 'numbered_list';
    case OpinionCards = 'opinion_cards';
    case MediaGrid = 'media_grid';

    public function label(): string
    {
        return match ($this) {
            self::HeroPrimary => 'Hero Utama (Lead + Sorotan Samping)',
            self::Grid4 => 'Grid 4 Kolom',
            self::Grid3 => 'Grid 3 Kolom',
            self::LeadPlusList => 'Lead Utama + Daftar Artikel',
            self::HorizontalList => 'Daftar Horizontal Kompak',
            self::NumberedList => 'Daftar Bernomor (Ranking 1-5)',
            self::OpinionCards => 'Kartu Kolom Opini (Penulis)',
            self::MediaGrid => 'Grid Media (Video / Foto 16:9)',
        };
    }
}
