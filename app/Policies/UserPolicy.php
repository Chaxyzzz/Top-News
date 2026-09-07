<?php

namespace App\Policies;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermission('users.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Target is Super Admin: only Super Admin can edit
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermission('users.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // User cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Admin cannot delete Super Admin
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        // Last Super Admin cannot be deleted
        if ($model->isSuperAdmin() && $this->isLastActiveSuperAdmin($model)) {
            return false;
        }

        return $user->hasPermission('users.delete');
    }

    /**
     * Determine whether the user can suspend the model.
     */
    public function suspend(User $user, User $model): bool
    {
        // Cannot suspend self
        if ($user->id === $model->id) {
            return false;
        }

        // Admin cannot suspend Super Admin
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        // Last Super Admin cannot be suspended
        if ($model->isSuperAdmin() && $this->isLastActiveSuperAdmin($model)) {
            return false;
        }

        return $user->hasPermission('users.suspend');
    }

    /**
     * Determine whether the user can activate the model.
     */
    public function activate(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermission('users.suspend');
    }

    /**
     * Determine whether the user can assign a specific role.
     */
    public function assignRole(User $user, User $model, Role $role): bool
    {
        // Only Super Admin can assign the super_admin role
        if ($role->name === 'super_admin' && ! $user->isSuperAdmin()) {
            return false;
        }

        // Cannot demote the last Super Admin
        if ($model->isSuperAdmin() && $role->name !== 'super_admin' && $this->isLastActiveSuperAdmin($model)) {
            return false;
        }

        return $user->hasPermission('users.create') || $user->hasPermission('users.update');
    }

    /**
     * Helper to check if model is the last active super admin.
     */
    protected function isLastActiveSuperAdmin(User $model): bool
    {
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if (! $superAdminRole) {
            return false;
        }

        $activeSuperAdminsCount = User::whereHas('roles', function ($q) use ($superAdminRole) {
            $q->where('roles.id', $superAdminRole->id);
        })->where('status', UserStatus::Active)->count();

        return $activeSuperAdminsCount <= 1;
    }
}
