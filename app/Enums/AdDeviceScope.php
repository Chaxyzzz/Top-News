<?php

namespace App\Enums;

enum AdDeviceScope: string
{
    case All = 'all';
    case Desktop = 'desktop';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Semua Perangkat',
            self::Desktop => 'Hanya Desktop',
            self::Mobile => 'Hanya Ponsel / Tablet',
        };
    }
}
