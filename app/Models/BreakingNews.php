<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BreakingNews extends Model
{
    use HasFactory;

    protected $table = 'breaking_news';

    protected $fillable = [
        'uuid',
        'headline',
        'article_id',
        'external_url',
        'starts_at',
        'ends_at',
        'priority',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BreakingNews $breaking) {
            if (empty($breaking->uuid)) {
                $breaking->uuid = (string) Str::uuid();
            }
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for currently active breaking news within scheduled time window.
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $now);
            })
            ->orderByDesc('priority')
            ->orderByDesc('starts_at')
            ->latest('created_at');
    }

    /**
     * Resolve public destination URL for this breaking item.
     */
    public function getDestinationUrl(): ?string
    {
        if ($this->article_id && $this->article) {
            if ($this->article->isPublished()) {
                return route('news.show', $this->article->slug);
            }

            return null; // Unpublished article is hidden/unclickable
        }

        if ($this->external_url) {
            return $this->external_url;
        }

        return null;
    }

    /**
     * Determine if this breaking item is valid to be displayed publicly.
     */
    public function isEligibleForPublic(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        if ($this->article_id) {
            return $this->article && $this->article->isPublished();
        }

        return ! empty($this->headline);
    }
}
