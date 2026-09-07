<?php

namespace App\Enums;

enum HomepageSourceType: string
{
    case Automatic = 'automatic';
    case Category = 'category';
    case Manual = 'manual';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => 'Otomatis (Algoritma / Waktu)',
            self::Category => 'Berdasarkan Rubrik / Kategori',
            self::Manual => 'Kurasi Manual Redaksi',
            self::System => 'Sistem Khusus',
        };
    }
}
