<?php

namespace App\Http\Requests;

use App\Enums\PageStatus;
use App\Enums\PageType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('pages.update') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $page = $this->route('page');

        return [
            'title' => 'required|string|max:200',
            'slug' => ['required', 'string', 'max:200', Rule::unique('pages', 'slug')->ignore($page->id)],
            'page_type' => ['nullable', new Enum(PageType::class), Rule::unique('pages', 'page_type')->ignore($page->id)],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => ['required', new Enum(PageStatus::class)],
            'show_in_search' => 'nullable|boolean',
            'seo_title' => 'nullable|string|max:200',
            'seo_description' => 'nullable|string|max:500',
        ];
    }
}
