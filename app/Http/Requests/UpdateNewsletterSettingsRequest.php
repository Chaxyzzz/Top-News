<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsletterSettingsRequest extends FormRequest
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
            'enabled' => 'nullable|boolean',
            'double_opt_in' => 'nullable|boolean',
            'default_sender_name' => 'required|string|max:100',
            'default_reply_to' => 'required|email|max:150',
            'cta_title' => 'required|string|max:150',
            'cta_description' => 'required|string|max:300',
        ];
    }
}
