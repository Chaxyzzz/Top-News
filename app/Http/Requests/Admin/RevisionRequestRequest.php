<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RevisionRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('requestRevision', $this->route('article'));
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }
}
