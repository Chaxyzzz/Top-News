<?php

namespace App\Http\Requests;

use App\Enums\HomepageLayoutVariant;
use App\Enums\HomepageSectionType;
use App\Enums\HomepageSourceType;
use App\Models\HomepageSection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HomepageSection::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'key' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:homepage_sections,key'],
            'section_type' => ['required', Rule::enum(HomepageSectionType::class)],
            'source_type' => ['required', Rule::enum(HomepageSourceType::class)],
            'category_id' => [
                Rule::requiredIf($this->input('source_type') === HomepageSourceType::Category->value),
                'nullable',
                'exists:categories,id',
            ],
            'layout_variant' => ['required', Rule::enum(HomepageLayoutVariant::class)],
            'item_limit' => ['required', 'integer', 'min:1', 'max:12'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
