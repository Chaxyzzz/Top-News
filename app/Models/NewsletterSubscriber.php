<?php

namespace App\Models;

use App\Enums\NewsletterSubscriberStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'email',
        'name',
        'status',
        'verification_token_hash',
        'verified_at',
        'subscribed_at',
        'unsubscribed_at',
        'source',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => NewsletterSubscriberStatus::class,
        'verified_at' => 'datetime',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Bootstrap the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (NewsletterSubscriber $sub) {
            if (empty($sub->uuid)) {
                $sub->uuid = (string) Str::uuid();
            }
            if (empty($sub->subscribed_at)) {
                $sub->subscribed_at = now();
            }
            $sub->email = Str::lower(trim($sub->email));
        });
    }

    /**
     * Scope to active subscribers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', NewsletterSubscriberStatus::Active);
    }

    /**
     * Scope to pending subscribers.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', NewsletterSubscriberStatus::Pending);
    }

    /**
     * Scope to unsubscribed.
     */
    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->where('status', NewsletterSubscriberStatus::Unsubscribed);
    }

    /**
     * Scope to blocked.
     */
    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', NewsletterSubscriberStatus::Blocked);
    }
}
