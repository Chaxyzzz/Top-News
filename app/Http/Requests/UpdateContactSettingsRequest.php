<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateContactSettingsRequest extends FormRequest
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
            'public_email' => 'required|email|max:150',
            'public_phone' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'office_address' => 'required|string|max:500',
            'location' => 'nullable|string|max:200',
            'business_hours' => 'nullable|string|max:100',
            'google_maps_url' => 'nullable|url|max:500',
        ];
    }
}
