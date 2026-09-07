<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Article;
use App\Models\ArticleRevision;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of staff users.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::with('roles', 'creator')->latest();

        // Search by name, username, or email
        if ($search = $request->query('search')) {
            $searchTerm = '%'.trim($search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('username', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            });
        }

        // Role Filter
        if ($roleId = $request->query('role_id')) {
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        // Status Filter
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();
        $statuses = UserStatus::cases();

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    /**
     * Show the form for creating a new staff user.
     */
    public function create(): View
    {
        $this->authorize('create', User::class);

        $roles = Role::all();
        $statuses = UserStatus::cases();

        return view('admin.users.create', compact('roles', 'statuses'));
    }

    /**
     * Store a newly created staff user.
     */
    public function store(StoreUserRequest $request, AuditLogService $auditLogger): RedirectResponse
    {
        $validated = $request->validated();
        $role = Role::findOrFail($validated['role_id']);

        // Check if actor has permission to assign this role (e.g. Super Admin role restriction)
        if ($role->name === 'super_admin' && ! Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat memberikan role Super Admin.');
        }

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => trim($validated['name']),
            'username' => Str::lower(trim($validated['username'])),
            'email' => Str::lower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
            'created_by' => Auth::id(),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($role);

        $auditLogger->log(
            action: 'user.created',
            description: "Akun pengguna baru [{$user->email}] dibuat dengan role [{$role->label}]",
            entityType: User::class,
            entityId: $user->id,
            user: Auth::user()
        );

        return redirect()->route('admin.users.index')->with('success', "Pengguna [{$user->name}] berhasil ditambahkan.");
    }

    /**
     * Display the specified staff user.
     */
    public function show(User $user): View
    {
        $this->authorize('view', $user);

        $user->load('roles', 'creator');

        $auditLogs = AuditLog::where('user_id', $user->id)
            ->orWhere(function ($q) use ($user) {
                $q->where('entity_type', User::class)->where('entity_id', $user->id);
            })
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'auditLogs'));
    }

    /**
     * Show the form for editing the staff user.
     */
    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $roles = Role::all();
        $statuses = UserStatus::cases();

        return view('admin.users.edit', compact('user', 'roles', 'statuses'));
    }

    /**
     * Update the specified staff user.
     */
    public function update(UpdateUserRequest $request, User $user, AuditLogService $auditLogger): RedirectResponse
    {
        $validated = $request->validated();
        $newRole = Role::findOrFail($validated['role_id']);

        // Check if actor has permission to assign this role
        if (! $request->user()->can('assignRole', [$user, $newRole])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah role ini.');
        }

        $user->fill([
            'name' => trim($validated['name']),
            'username' => Str::lower(trim($validated['username'])),
            'email' => Str::lower(trim($validated['email'])),
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$newRole->id]);

        $auditLogger->log(
            action: 'user.updated',
            description: "Data pengguna [{$user->email}] diperbarui dengan role [{$newRole->label}]",
            entityType: User::class,
            entityId: $user->id,
            user: Auth::user()
        );

        return redirect()->route('admin.users.index')->with('success', "Data pengguna [{$user->name}] berhasil diperbarui.");
    }

    /**
     * Suspend the specified user account.
     */
    public function suspend(Request $request, User $user, AuditLogService $auditLogger): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $user->update(['status' => UserStatus::Suspended]);

        $auditLogger->log(
            action: 'user.suspended',
            description: "Akun pengguna [{$user->email}] ditangguhkan oleh [".Auth::user()->email.']',
            entityType: User::class,
            entityId: $user->id,
            user: Auth::user()
        );

        return back()->with('warning', "Akun [{$user->name}] berhasil ditangguhkan.");
    }

    /**
     * Activate the specified user account.
     */
    public function activate(Request $request, User $user, AuditLogService $auditLogger): RedirectResponse
    {
        $this->authorize('activate', $user);

        $user->update(['status' => UserStatus::Active]);

        $auditLogger->log(
            action: 'user.activated',
            description: "Akun pengguna [{$user->email}] diaktifkan oleh [".Auth::user()->email.']',
            entityType: User::class,
            entityId: $user->id,
            user: Auth::user()
        );

        return back()->with('success', "Akun [{$user->name}] berhasil diaktifkan kembali.");
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
    public function destroy(Request $request, User $user, AuditLogService $auditLogger): RedirectResponse
    {
        $this->authorize('delete', $user);

        // Safeguard 1: Cannot delete self
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Safeguard 2: Cannot delete last remaining active Super Admin
        if ($user->isSuperAdmin()) {
            $activeSuperAdminsCount = User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))
                ->where('status', UserStatus::Active)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperAdminsCount < 1) {
                return redirect()->route('admin.users.index')->with('error', 'Gagal menghapus: Harus tersisa minimal satu Super Admin aktif dalam sistem.');
            }
        }

        $userName = $user->name;
        $userEmail = $user->email;

        DB::transaction(function () use ($user) {
            // Reassign author_id to deleting admin since author_id is NOT NULL
            Article::where('author_id', $user->id)->update(['author_id' => Auth::id()]);
            Article::where('editor_id', $user->id)->update(['editor_id' => null]);
            ArticleRevision::where('user_id', $user->id)->update(['user_id' => null]);

            $user->roles()->detach();
            $user->forceDelete();
        });

        $auditLogger->log(
            action: 'user.permanently_deleted',
            description: "Akun pengguna [{$userEmail}] dihapus permanen dari sistem oleh [".Auth::user()->email.']',
            entityType: User::class,
            entityId: $user->id,
            user: Auth::user()
        );

        return redirect()->route('admin.users.index')->with('success', "Pengguna [{$userName}] berhasil dihapus permanen dari sistem.");
    }
}
