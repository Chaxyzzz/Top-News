<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRolesAndPermissions
{
    /**
     * The roles that belong to the user.
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Check if user has a specific role or any of the given roles.
     *
     * @param  string|array<int, string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $roleList = is_array($roles) ? $roles : func_get_args();

        return $this->roles->pluck('name')->intersect($roleList)->isNotEmpty();
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Super Admin bypasses all individual permission checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->getAllPermissions()->contains('name', $permission);
    }

    /**
     * Get all permissions assigned to user through their roles.
     *
     * @return Collection<int, Permission>
     */
    public function getAllPermissions(): Collection
    {
        return $this->roles->flatMap(function (Role $role) {
            return $role->permissions;
        })->unique('id');
    }

    /**
     * Assign a role to user.
     */
    public function assignRole(Role|string $role): void
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->firstOrFail() : $role;

        if (! $this->roles->contains('id', $roleModel->id)) {
            $this->roles()->attach($roleModel->id);
            $this->load('roles');
        }
    }

    /**
     * Sync user roles.
     *
     * @param  array<int, int|string|Role>  $roles
     */
    public function syncRoles(array $roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if ($role instanceof Role) {
                return $role->id;
            }
            if (is_numeric($role)) {
                return (int) $role;
            }

            return Role::where('name', $role)->value('id');
        })->filter()->all();

        $this->roles()->sync($roleIds);
        $this->load('roles');
    }

    /**
     * Get primary role for display.
     */
    public function getPrimaryRoleAttribute(): ?Role
    {
        return $this->roles->first();
    }
}
