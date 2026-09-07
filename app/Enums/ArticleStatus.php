<?php

namespace App\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case InReview = 'in_review';
    case RevisionRequested = 'revision_requested';
    case Approved = 'approved';
    case Scheduled = 'scheduled';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * Get the human-readable Indonesian label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Submitted => 'Menunggu Review',
            self::InReview => 'Sedang Ditinjau',
            self::RevisionRequested => 'Perlu Revisi',
            self::Approved => 'Disetujui',
            self::Scheduled => 'Terjadwal',
            self::Published => 'Terbit',
            self::Archived => 'Diarsipkan',
        };
    }

    /**
     * Get the badge color variant.
     */
    public function badgeVariant(): string
    {
        return match ($this) {
            self::Draft => 'subtle',
            self::Submitted => 'warning',
            self::InReview => 'primary',
            self::RevisionRequested => 'danger',
            self::Approved => 'warning',
            self::Scheduled => 'primary',
            self::Published => 'success',
            self::Archived => 'dark',
        };
    }

    /**
     * Check if article is publicly viewable.
     */
    public function isPubliclyVisible(): bool
    {
        return $this === self::Published;
    }
}
