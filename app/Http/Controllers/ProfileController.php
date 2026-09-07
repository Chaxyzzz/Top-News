<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user()->load('roles'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request, AuditLogService $auditLogger): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if present
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $auditLogger->log(
            action: 'profile.updated',
            description: "Profil user [{$user->email}] diperbarui",
            entityType: User::class,
            entityId: $user->id,
            user: $user
        );

        return redirect()->route('profile.edit')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request, AuditLogService $auditLogger): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $auditLogger->log(
            action: 'profile.password_changed',
            description: "Kata sandi user [{$user->email}] berhasil diubah",
            entityType: User::class,
            entityId: $user->id,
            user: $user
        );

        return redirect()->route('profile.edit')->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
