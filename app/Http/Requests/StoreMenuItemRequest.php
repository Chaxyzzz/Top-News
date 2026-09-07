<?php

namespace App\Http\Requests;

use App\Enums\MenuLinkType;
use App\Models\MenuItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public const ALLOWED_ROUTES = [
        'home',
        'latest',
        'opinion.index',
        'trending.index',
        'popular.index',
        'editors-choice.index',
        'video.index',
        'photo-story.index',
        'about',
        'contact',
        'search',
    ];

    public function authorize(): bool
    {
        return $this->user()?->can('create', MenuItem::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'menu_id' => ['required', 'exists:menus,id'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'label' => ['required', 'string', 'max:100'],
            'link_type' => ['required', Rule::enum(MenuLinkType::class)],
            'category_id' => [
                Rule::requiredIf($this->input('link_type') === MenuLinkType::Category->value),
                'nullable',
                'exists:categories,id',
            ],
            'route_name' => [
                Rule::requiredIf($this->input('link_type') === MenuLinkType::Route->value),
                'nullable',
                Rule::in(self::ALLOWED_ROUTES),
            ],
            'url' => [
                Rule::requiredIf($this->input('link_type') === MenuLinkType::Url->value),
                'nullable',
                'url',
                'regex:/^https?:\/\//i',
            ],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'open_new_tab' => ['nullable', 'boolean'],
        ];
    }
}
