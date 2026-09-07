<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.update') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'about_text' => 'required|string|max:500',
            'copyright_text' => 'nullable|string|max:300',
            'developer_label' => 'nullable|string|max:100',
            'developer_name' => 'nullable|string|max:100',
        ];
    }
}
