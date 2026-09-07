<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case New = 'new';
    case Read = 'read';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Spam = 'spam';

    /**
     * Get human-readable label in Indonesian.
     */
    public function label(): string
    {
        return match ($this) {
            self::New => 'Pesan Baru',
            self::Read => 'Telah Dibaca',
            self::InProgress => 'Sedang Diproses',
            self::Resolved => 'Terselesaikan',
            self::Spam => 'Spam',
        };
    }

    /**
     * Get badge CSS classes.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::New => 'bg-blue-100 text-blue-800 border-blue-200',
            self::Read => 'bg-neutral-100 text-neutral-800 border-neutral-200',
            self::InProgress => 'bg-amber-100 text-amber-800 border-amber-200',
            self::Resolved => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::Spam => 'bg-rose-100 text-rose-800 border-rose-200',
        };
    }
}
