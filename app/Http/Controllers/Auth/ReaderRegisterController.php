<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterReaderRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ReaderRegisterController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    /**
     * Show reader registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isReader()
                ? redirect()->route('account.bookmarks')
                : redirect()->route('admin.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle public reader registration.
     */
    public function register(RegisterReaderRequest $request): RedirectResponse
    {
        $username = $request->input('username');
        if (empty($username)) {
            $baseUsername = Str::slug($request->input('name'), '');
            $username = $baseUsername.'_'.Str::lower(Str::random(4));
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername.'_'.Str::lower(Str::random(4));
            }
        }

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->input('name'),
            'username' => $username,
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'status' => UserStatus::Active,
            'account_type' => 'reader',
        ]);

        $readerRole = Role::where('name', 'reader')->first();
        if ($readerRole) {
            $user->assignRole($readerRole);
        }

        $this->auditLog->log(
            'reader.registered',
            $user,
            "Pembaca baru terdaftar: {$user->name} ({$user->email}).",
            $user
        );

        Auth::login($user);

        return redirect()->intended(route('account.bookmarks'))
            ->with('success', "Selamat datang di TopNews, {$user->name}! Akun pembaca Anda telah aktif.");
    }
}
