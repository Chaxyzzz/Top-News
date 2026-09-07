<?php

namespace App\Http\Controllers;

use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ReaderAccountController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    /**
     * Display list of bookmarked articles.
     */
    public function bookmarks(Request $request): View
    {
        $user = Auth::user();

        $bookmarks = $user->bookmarks()
            ->with(['article' => function ($q) {
                $q->published()->with(['category', 'author', 'featuredMedia']);
            }])
            ->latest('created_at')
            ->paginate(12);

        return view('account.bookmarks', compact('bookmarks', 'user'));
    }

    /**
     * Display list of comments submitted by the user.
     */
    public function comments(Request $request): View
    {
        $user = Auth::user();

        $comments = $user->comments()
            ->with(['article:id,title,slug,status,published_at', 'parent:id,body,user_id'])
            ->latest()
            ->paginate(15);

        return view('account.comments', compact('comments', 'user'));
    }

    /**
     * Display user profile information.
     */
    public function profile(): View
    {
        $user = Auth::user();

        return view('account.profile', compact('user'));
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username,'.$user->id],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Nama pengguna wajib diisi.',
            'username.unique' => 'Nama pengguna telah digunakan oleh akun lain.',
        ]);

        $user->update($validated);

        return redirect()->route('account.profile')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Display security / password settings.
     */
    public function security(): View
    {
        $user = Auth::user();

        return view('account.security', compact('user'));
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        $this->auditLog->log(
            'auth.password_changed',
            $user,
            "Pengguna {$user->name} mengubah kata sandi akun.",
            $user
        );

        return redirect()->route('account.security')
            ->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
