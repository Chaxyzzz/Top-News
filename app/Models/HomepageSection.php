<?php

namespace App\Models;

use App\Enums\HomepageLayoutVariant;
use App\Enums\HomepageSectionType;
use App\Enums\HomepageSourceType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class HomepageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'key',
        'title',
        'subtitle',
        'section_type',
        'source_type',
        'category_id',
        'is_active',
        'sort_order',
        'item_limit',
        'layout_variant',
        'settings',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'section_type' => HomepageSectionType::class,
            'source_type' => HomepageSourceType::class,
            'layout_variant' => HomepageLayoutVariant::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'item_limit' => 'integer',
            'settings' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (HomepageSection $section) {
            if (empty($section->uuid)) {
                $section->uuid = (string) Str::uuid();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function sectionArticles(): HasMany
    {
        return $this->hasMany(HomepageSectionArticle::class)->orderBy('sort_order');
    }

    /**
     * Curated articles assigned to this section.
     */
    public function curatedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'homepage_section_articles')
            ->withPivot(['sort_order', 'created_at'])
            ->orderByPivot('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
