<?php

namespace App\Enums;

enum NewsletterSubscriberStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Unsubscribed = 'unsubscribed';
    case Blocked = 'blocked';

    /**
     * Get human-readable label in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Active => 'Aktif',
            self::Unsubscribed => 'Berhenti Langganan',
            self::Blocked => 'Diblokir',
        };
    }

    /**
     * Get badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Active => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Unsubscribed => 'bg-neutral-100 text-neutral-600 border-neutral-200',
            self::Blocked => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
