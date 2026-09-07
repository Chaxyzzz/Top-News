<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User|null $targetUser */
        $targetUser = $this->route('user');

        return $targetUser ? ($this->user()?->can('update', $targetUser) ?? false) : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var User $targetUser */
        $targetUser = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($targetUser->id),
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($targetUser->id),
            ],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', new Enum(UserStatus::class)],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, strip, atau garis bawah.',
            'username.unique' => 'Username ini sudah digunakan oleh akun lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar dalam sistem.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
        ];
    }
}
