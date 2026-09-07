<?php

namespace App\Enums;

enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * Get human-readable label in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Published => 'Diterbitkan',
            self::Archived => 'Diarsipkan',
        };
    }

    /**
     * Get badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Draft => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Published => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Archived => 'bg-neutral-100 text-neutral-600 border-neutral-200',
        };
    }
}
