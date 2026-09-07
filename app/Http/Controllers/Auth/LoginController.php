<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()->isReader()
                ? redirect()->route('account.bookmarks')
                : redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request, AuditLogService $auditLogger): RedirectResponse
    {
        $request->authenticate($auditLogger);

        $request->session()->regenerate();

        $user = Auth::user();
        $targetRoute = $user->isReader() ? route('account.bookmarks') : route('admin.dashboard');

        return redirect()->intended($targetRoute)->with('success', 'Selamat datang kembali, '.$user->name.'.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request, AuditLogService $auditLogger): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            $auditLogger->log(
                action: 'auth.logout',
                description: "User [{$user->email}] logout",
                entityType: get_class($user),
                entityId: $user->id,
                user: $user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Anda telah berhasil keluar dari sesi.');
    }
}
