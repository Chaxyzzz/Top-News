<?php

namespace App\Enums;

enum CommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Spam = 'spam';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Moderasi',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
            self::Spam => 'Spam',
        };
    }

    /**
     * Get the UI badge style variant.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
            self::Spam => 'neutral',
        };
    }
}
