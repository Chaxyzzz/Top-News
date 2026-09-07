<?php

namespace App\Models;

use App\Enums\MediaType;
use App\Services\SettingsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'disk',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'alt_text',
        'caption',
        'credit',
        'media_type',
        'variants',
        'uploaded_by',
    ];

    /**
     * Bootstrap model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Media $media) {
            if (empty($media->uuid)) {
                $media->uuid = (string) Str::uuid();
            }
            if (empty($media->disk)) {
                $media->disk = 'public';
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
            'media_type' => MediaType::class,
            'variants' => 'array',
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * Get the user who uploaded this media.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get articles where this media is the featured image.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'featured_media_id');
    }

    /**
     * Get galleries where this media is included.
     */
    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class, 'gallery_media')
            ->withPivot(['sort_order', 'caption_override', 'credit_override'])
            ->withTimestamps();
    }

    /**
     * Get galleries where this media is the cover image.
     */
    public function coverGalleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'cover_media_id');
    }

    /**
     * Get videos where this media is the custom thumbnail.
     */
    public function thumbnailVideos(): HasMany
    {
        return $this->hasMany(ArticleVideo::class, 'thumbnail_media_id');
    }

    /**
     * Get the full public URL of the original media.
     */
    public function getUrlAttribute(): string
    {
        if (Str::startsWith($this->path, ['http://', 'https://'])) {
            return $this->path;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the URL for a specific variant (thumbnail, medium, large, xlarge).
     */
    public function variantUrl(string $variant): string
    {
        if (! empty($this->variants[$variant])) {
            return Storage::disk($this->disk)->url($this->variants[$variant]);
        }

        return $this->url;
    }

    /**
     * Get thumbnail variant URL.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->variantUrl('thumbnail');
    }

    /**
     * Get medium variant URL.
     */
    public function getMediumUrlAttribute(): string
    {
        return $this->variantUrl('medium');
    }

    /**
     * Get large variant URL.
     */
    public function getLargeUrlAttribute(): string
    {
        return $this->variantUrl('large');
    }

    /**
     * Get human readable file size.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    /**
     * Get dimensions formatted string.
     */
    public function getDimensionsAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return "{$this->width} × {$this->height} px";
        }

        return null;
    }

    /**
     * Check if the media is currently used in articles, galleries, or videos.
     */
    public function isUsed(): bool
    {
        return $this->articles()->exists()
            || $this->galleries()->exists()
            || $this->coverGalleries()->exists()
            || $this->thumbnailVideos()->exists()
            || app(SettingsService::class)->isMediaUsedInSettings($this->id);
    }

    /**
     * Get total usage count.
     */
    public function getUsageCountAttribute(): int
    {
        return $this->articles()->count()
            + $this->galleries()->count()
            + $this->coverGalleries()->count()
            + $this->thumbnailVideos()->count();
    }

    /**
     * Scope query to images.
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->where('media_type', MediaType::Image);
    }

    /**
     * Scope query for search.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        $term = '%'.trim($term).'%';

        return $query->where(function ($q) use ($term) {
            $q->where('original_filename', 'like', $term)
                ->orWhere('alt_text', 'like', $term)
                ->orWhere('caption', 'like', $term)
                ->orWhere('credit', 'like', $term);
        });
    }
}
