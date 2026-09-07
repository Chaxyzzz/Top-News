<?php

namespace App\Http\Requests;

use App\Models\Advertisement;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Advertisement::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'campaign_id' => ['required', 'exists:ad_campaigns,id'],
            'ad_slot_id' => ['required', 'exists:ad_slots,id'],
            'name' => ['required', 'string', 'max:255'],
            'media_id' => ['nullable', 'exists:media,id'],
            'headline' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1000'],
            'destination_url' => ['required', 'url', 'regex:/^https?:\/\//i'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }
}
