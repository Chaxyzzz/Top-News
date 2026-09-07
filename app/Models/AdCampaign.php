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

class AdCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'advertiser',
        'status',
        'starts_at',
        'ends_at',
        'budget_note',
        'contact_name',
        'contact_email',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AdCampaignStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AdCampaign $campaign) {
            if (empty($campaign->uuid)) {
                $campaign->uuid = (string) Str::uuid();
            }
        });
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'campaign_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('status', AdCampaignStatus::Active->value)
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $now);
            });
    }

    /**
     * Compute effective status (e.g. Expired if ends_at has passed).
     */
    public function getEffectiveStatusLabel(): string
    {
        if ($this->status === AdCampaignStatus::Active && $this->ends_at && $this->ends_at->isPast()) {
            return 'Berakhir (Jadwal Lewat)';
        }

        return $this->status->label();
    }
}
