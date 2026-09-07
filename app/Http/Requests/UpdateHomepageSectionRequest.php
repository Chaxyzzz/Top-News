<?php

namespace App\Http\Requests;

use App\Enums\HomepageLayoutVariant;
use App\Enums\HomepageSourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHomepageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $section = $this->route('section');

        return $section && $this->user()?->can('update', $section);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
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
