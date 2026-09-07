<?php

namespace App\Http\Requests;

use App\Enums\PageStatus;
use App\Enums\PageType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('pages.create') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:pages,slug',
            'page_type' => ['nullable', new Enum(PageType::class), 'unique:pages,page_type'],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => ['required', new Enum(PageStatus::class)],
            'show_in_search' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:200',
            'seo_description' => 'nullable|string|max:500',
        ];
    }
}
