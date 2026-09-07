<?php

namespace App\Http\Requests\Admin;

use App\Enums\ArticleType;
use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Article::class);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'content_type' => ['required', new Enum(ArticleType::class)],
            'author_id' => ['nullable', 'exists:users,id'],
            'featured_media_id' => ['nullable', 'exists:media,id'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'featured_image_caption' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:500'],
            'duration_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'gallery_id' => ['nullable', 'exists:galleries,id'],
            'source_name' => ['nullable', 'string', 'max:150'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'is_breaking' => ['nullable', 'boolean'],
            'is_editor_choice' => ['nullable', 'boolean'],
            'is_sponsored' => ['nullable', 'boolean'],
            'homepage_priority' => ['nullable', 'integer', 'min:0', 'max:100'],
            'allow_comments' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_index' => ['nullable', 'boolean'],
        ];
    }
}
