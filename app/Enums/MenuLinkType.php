<?php

namespace App\Enums;

enum MenuLinkType: string
{
    case Category = 'category';
    case Route = 'route';
    case Url = 'url';

    public function label(): string
    {
        return match ($this) {
            self::Category => 'Rubrik / Kategori Berita',
            self::Route => 'Halaman Sistem / Named Route',
            self::Url => 'Tautan Kustom / Eksternal',
        };
    }
}
