<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles and permission matrix.
     */
    public function index(): View
    {
        if (! Auth::user()->hasPermission('roles.view')) {
            abort(403, 'Akses tidak diizinkan untuk melihat manajemen role.');
        }

        $roles = Role::with(['permissions', 'users'])->get();
        $permissions = Permission::all()->groupBy('group');

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    /**
     * Show the form for editing permissions for a specific role.
     */
    public function edit(Role $role): View
    {
        if (! Auth::user()->hasPermission('roles.manage')) {
            abort(403, 'Akses tidak diizinkan untuk mengelola izin role.');
        }

        $role->load('permissions');
        $permissions = Permission::all()->groupBy('group');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the permissions for the specified role.
     */
    public function update(Request $request, Role $role, AuditLogService $auditLogger): RedirectResponse
    {
        if (! Auth::user()->hasPermission('roles.manage')) {
            abort(403, 'Akses tidak diizinkan untuk mengelola izin role.');
        }

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->has('description')) {
            $role->update(['description' => $validated['description']]);
        }

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        $auditLogger->log(
            action: 'role.permissions_updated',
            description: "Izin untuk role [{$role->label}] diperbarui oleh [".Auth::user()->email.']',
            entityType: Role::class,
            entityId: $role->id,
            user: Auth::user()
        );

        return redirect()->route('admin.roles.index')->with('success', "Izin untuk role [{$role->label}] berhasil diperbarui.");
    }
}
