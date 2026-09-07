<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEditorialSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.editorial') || $this->user()?->hasPermission('settings.update');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'default_allow_comments' => 'nullable|boolean',
            'default_reading_words_per_minute' => 'required|integer|min:100|max:500',
            'default_articles_per_page' => 'required|integer|min:6|max:50',
        ];
    }
}
