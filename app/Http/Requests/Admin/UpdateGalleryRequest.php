<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('gallery'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $galleryId = $this->route('gallery')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', "unique:galleries,slug,{$galleryId}"],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover_media_id' => ['nullable', 'exists:media,id'],
            'photographer_name' => ['nullable', 'string', 'max:255'],
            'photographer_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'media_ids' => ['nullable', 'array'],
            'media_ids.*' => ['exists:media,id'],
            'captions' => ['nullable', 'array'],
            'credits' => ['nullable', 'array'],
        ];
    }
}
