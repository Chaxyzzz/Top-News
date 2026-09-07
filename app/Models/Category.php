<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'accent_color',
        'sort_order',
        'show_on_homepage',
        'homepage_order',
        'show_in_navigation',
        'navigation_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_on_homepage' => 'boolean',
            'homepage_order' => 'integer',
            'show_in_navigation' => 'boolean',
            'navigation_order' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot model events for automatic slug generation.
     */
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for categories displayed on the public homepage.
     */
    public function scopeHomepage(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('show_on_homepage', true)
            ->orderBy('homepage_order', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    /**
     * Scope for categories displayed in main public navigation.
     */
    public function scopeNavigation(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('show_in_navigation', true)
            ->orderBy('navigation_order', 'asc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    /**
     * Scope ordered by sort priority.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get sub-categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->ordered();
    }

    /**
     * Get all articles within this category.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Homepage sections configured for this category.
     *
     * @return HasMany<HomepageSection>
     */
    public function homepageSections(): HasMany
    {
        return $this->hasMany(HomepageSection::class);
    }

    /**
     * Menu items linked to this category.
     *
     * @return HasMany<MenuItem>
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
