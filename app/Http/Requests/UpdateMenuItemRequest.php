<?php

namespace App\Http\Requests;

use App\Enums\MenuLinkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');

        return $item && $this->user()?->can('update', $item);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
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
                Rule::in(StoreMenuItemRequest::ALLOWED_ROUTES),
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
