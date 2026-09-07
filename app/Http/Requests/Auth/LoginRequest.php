<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'remember' => $this->boolean('remember'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Authenticate the user and handle throttling.
     *
     * @throws ValidationException
     */
    public function authenticate(AuditLogService $auditLogger): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim($this->input('login'));
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Check if login input is email or username
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Check user existence & status before or alongside auth
        $user = User::where($fieldType, Str::lower($login))
            ->orWhere($fieldType, $login)
            ->first();

        if ($user && $user->status === UserStatus::Suspended) {
            $auditLogger->log(
                action: 'auth.login_blocked_suspended',
                description: "Percobaan login ditolak: akun ditangguhkan [{$user->email}]",
                entityType: User::class,
                entityId: $user->id
            );

            throw ValidationException::withMessages([
                'login' => 'Akun Anda saat ini ditangguhkan. Silakan hubungi Administrator TopNews.',
            ]);
        }

        if ($user && $user->status === UserStatus::Inactive) {
            $auditLogger->log(
                action: 'auth.login_blocked_inactive',
                description: "Percobaan login ditolak: akun nonaktif [{$user->email}]",
                entityType: User::class,
                entityId: $user->id
            );

            throw ValidationException::withMessages([
                'login' => 'Akun Anda tidak aktif. Silakan hubungi Administrator TopNews.',
            ]);
        }

        $credentials = [$fieldType => $user ? $user->$fieldType : Str::lower($login), 'password' => $password];

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey(), 60);

            $auditLogger->log(
                action: 'auth.login_failed',
                description: "Percobaan login gagal untuk identitas: {$login}",
                metadata: ['login_field' => $fieldType]
            );

            throw ValidationException::withMessages([
                'login' => 'Identitas atau kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Update login metadata
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();
        $authenticatedUser->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $this->ip(),
            'last_login_user_agent' => substr((string) $this->userAgent(), 0, 500),
        ])->save();

        $auditLogger->log(
            action: 'auth.login_success',
            description: "Login berhasil untuk user [{$authenticatedUser->email}]",
            entityType: User::class,
            entityId: $authenticatedUser->id,
            user: $authenticatedUser
        );
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => "Terlalu banyak percobaan login. Silakan coba kembali dalam {$seconds} detik.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
