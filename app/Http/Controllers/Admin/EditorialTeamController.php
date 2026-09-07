<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorialTeamController extends Controller
{
    /**
     * Display the editorial team management screen.
     */
    public function index(): View
    {
        $user = Auth::user();
        if (! $user || ! $user->hasPermission('editorial_team.manage')) {
            abort(403, 'Anda tidak memiliki hak akses mengelola tim redaksi publik.');
        }

        $staffUsers = User::where('status', UserStatus::Active)
            ->where('account_type', 'staff')
            ->orderBy('editorial_team_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.editorial-team.index', compact('staffUsers'));
    }

    /**
     * Update public editorial team visibility and ordering.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->hasPermission('editorial_team.manage')) {
            abort(403);
        }

        $validated = $request->validate([
            'members' => 'required|array',
            'members.*.id' => 'required|integer|exists:users,id',
            'members.*.public_title' => 'nullable|string|max:100',
            'members.*.bio' => 'nullable|string|max:1000',
            'members.*.show_on_editorial_team' => 'nullable|boolean',
            'members.*.editorial_team_order' => 'required|integer|min:0|max:999',
        ]);

        foreach ($validated['members'] as $memberData) {
            $staff = User::find($memberData['id']);
            if ($staff) {
                $staff->update([
                    'public_title' => $memberData['public_title'] ?? null,
                    'bio' => $memberData['bio'] ?? null,
                    'show_on_editorial_team' => ! empty($memberData['show_on_editorial_team']),
                    'editorial_team_order' => (int) ($memberData['editorial_team_order'] ?? 0),
                ]);
            }
        }

        if (class_exists(AuditLogger::class)) {
            AuditLogger::log('editorial_team.updated', Auth::user(), [
                'count' => count($validated['members']),
            ]);
        }

        return back()->with('success', 'Susunan dan profil tim redaksi publik berhasil diperbarui.');
    }
}
