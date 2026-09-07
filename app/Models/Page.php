<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Enums\PageType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'page_type',
        'excerpt',
        'content',
        'status',
        'show_in_search',
        'seo_title',
        'seo_description',
        'published_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'page_type' => PageType::class,
        'status' => PageStatus::class,
        'show_in_search' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Bootstrap the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Page $page) {
            if (empty($page->uuid)) {
                $page->uuid = (string) Str::uuid();
            }
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Author / Creator user.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Last editor user.
     *
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to published pages only.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published);
    }

    /**
     * Scope to system page type.
     */
    public function scopeSystemType(Builder $query, PageType|string $type): Builder
    {
        $val = $type instanceof PageType ? $type->value : $type;

        return $query->where('page_type', $val);
    }

    /**
     * Check if page is currently published.
     */
    public function isPublished(): bool
    {
        return $this->status === PageStatus::Published;
    }

    /**
     * Check if page is a core protected institutional page.
     */
    public function isProtectedCorePage(): bool
    {
        return $this->page_type !== null && $this->page_type->isCoreProtected();
    }
}
