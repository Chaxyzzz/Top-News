<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'description',
        'cover_media_id',
        'author_id',
        'photographer_name',
        'photographer_id',
        'status',
        'published_at',
    ];

    /**
     * Bootstrap model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Gallery $gallery) {
            if (empty($gallery->uuid)) {
                $gallery->uuid = (string) Str::uuid();
            }
            if (empty($gallery->slug)) {
                $gallery->slug = Str::slug($gallery->title);
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get associated media items in ordered sequence.
     */
    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'gallery_media')
            ->withPivot(['sort_order', 'caption_override', 'credit_override'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Get the cover media.
     */
    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    /**
     * Get the author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get internal photographer user if assigned.
     */
    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    /**
     * Get the photo story article associated with this gallery.
     */
    public function photoStory(): HasOne
    {
        return $this->hasOne(PhotoStory::class);
    }

    /**
     * Get the effective cover image URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->coverMedia) {
            return $this->coverMedia->large_url;
        }

        // Fallback to first image in gallery
        $first = $this->media->first();
        if ($first) {
            return $first->large_url;
        }

        return null;
    }

    /**
     * Get photographer display name.
     */
    public function getPhotographerDisplayAttribute(): string
    {
        if ($this->photographer) {
            return $this->photographer->name;
        }

        return $this->photographer_name ?: ($this->author?->name ?? 'Tim Foto TopNews');
    }

    /**
     * Scope query to published galleries.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
