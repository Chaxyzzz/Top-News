<?php

namespace App\Models;

use App\Enums\AdCampaignStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Advertisement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'campaign_id',
        'ad_slot_id',
        'name',
        'media_id',
        'headline',
        'body',
        'destination_url',
        'alt_text',
        'starts_at',
        'ends_at',
        'is_active',
        'priority',
        'impressions_count',
        'clicks_count',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'priority' => 'integer',
            'impressions_count' => 'integer',
            'clicks_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Advertisement $ad) {
            if (empty($ad->uuid)) {
                $ad->uuid = (string) Str::uuid();
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdCampaign::class, 'campaign_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(AdSlot::class, 'ad_slot_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(AdDailyStat::class)->orderByDesc('date');
    }

    /**
     * Scope for currently eligible active advertisements.
     */
    public function scopeCurrentlyEligible(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->whereHas('campaign', function (Builder $q) use ($now) {
                $q->where('status', AdCampaignStatus::Active->value)
                    ->where(function (Builder $sub) use ($now) {
                        $sub->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function (Builder $sub) use ($now) {
                        $sub->whereNull('ends_at')->orWhere('ends_at', '>', $now);
                    });
            })
            ->whereHas('slot', function (Builder $q) {
                $q->where('is_active', true);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $now);
            })
            ->orderByDesc('priority')
            ->latest('created_at');
    }

    /**
     * Compute click-through-rate percentage (CTR).
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions_count <= 0) {
            return 0.0;
        }

        return round(($this->clicks_count / $this->impressions_count) * 100, 2);
    }
}
