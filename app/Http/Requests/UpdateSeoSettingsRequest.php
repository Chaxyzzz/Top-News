<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('settings.seo') || $this->user()?->hasPermission('settings.update');
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'default_meta_title' => 'required|string|max:150',
            'default_meta_description' => 'required|string|max:300',
            'default_social_image_id' => 'nullable|integer|exists:media,id',
            'organization_name' => 'required|string|max:150',
        ];
    }
}
