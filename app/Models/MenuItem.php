<?php

namespace App\Models;

use App\Enums\MenuLinkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'link_type',
        'url',
        'category_id',
        'route_name',
        'sort_order',
        'is_active',
        'open_new_tab',
    ];

    protected function casts(): array
    {
        return [
            'link_type' => MenuLinkType::class,
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'open_new_tab' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Resolves the real URL for public rendering.
     */
    public function getResolvedUrl(): string
    {
        if ($this->link_type === MenuLinkType::Category && $this->category) {
            return route('category.show', $this->category->slug);
        }

        if ($this->link_type === MenuLinkType::Route && $this->route_name && Route::has($this->route_name)) {
            return route($this->route_name);
        }

        return $this->url ?? '#';
    }

    /**
     * Determine if this menu item matches current active request.
     */
    public function isCurrentActive(): bool
    {
        if ($this->link_type === MenuLinkType::Category && $this->category) {
            return request()->is('category/'.$this->category->slug);
        }

        if ($this->link_type === MenuLinkType::Route && $this->route_name) {
            return request()->routeIs($this->route_name);
        }

        if ($this->url) {
            $parsedPath = trim(parse_url($this->url, PHP_URL_PATH) ?? '', '/');

            return $parsedPath !== '' && request()->is($parsedPath);
        }

        return false;
    }
}
