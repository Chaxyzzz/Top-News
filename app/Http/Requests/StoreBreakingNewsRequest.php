<?php

namespace App\Http\Requests;

use App\Models\BreakingNews;
use Illuminate\Foundation\Http\FormRequest;

class StoreBreakingNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', BreakingNews::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'headline' => ['required', 'string', 'max:255'],
            'article_id' => ['nullable', 'exists:articles,id'],
            'external_url' => ['nullable', 'url', 'regex:/^https?:\/\//i'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
