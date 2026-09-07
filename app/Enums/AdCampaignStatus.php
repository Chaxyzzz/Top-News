<?php

namespace App\Enums;

enum AdCampaignStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Ended = 'ended';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf Kampanye',
            self::Active => 'Aktif Berjalan',
            self::Paused => 'Dijeda Sementara',
            self::Ended => 'Selesai / Berakhir',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-neutral-100 text-neutral-700',
            self::Active => 'bg-emerald-100 text-emerald-800',
            self::Paused => 'bg-amber-100 text-amber-800',
            self::Ended => 'bg-rose-100 text-rose-800',
        };
    }
}
