<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
            self::Suspended => 'Ditangguhkan',
        };
    }

    /**
     * Get the badge color variant.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'subtle',
            self::Suspended => 'breaking',
        };
    }

    /**
     * Check if user with this status is allowed to authenticate.
     */
    public function canLogin(): bool
    {
        return $this === self::Active;
    }
}
