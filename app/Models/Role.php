<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'description',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * The permissions that belong to the role.
     *
     * @return BelongsToMany<Permission, $this>
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /**
     * The users that belong to the role.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Give a permission to the role.
     */
    public function givePermissionTo(Permission|string $permission): void
    {
        $permModel = is_string($permission) ? Permission::where('name', $permission)->firstOrFail() : $permission;

        if (! $this->permissions->contains('id', $permModel->id)) {
            $this->permissions()->attach($permModel->id);
            $this->load('permissions');
        }
    }

    /**
     * Sync permissions for the role.
     *
     * @param  array<int, int|string|Permission>  $permissions
     */
    public function syncPermissions(array $permissions): void
    {
        $permIds = collect($permissions)->map(function ($perm) {
            if ($perm instanceof Permission) {
                return $perm->id;
            }
            if (is_numeric($perm)) {
                return (int) $perm;
            }

            return Permission::where('name', $perm)->value('id');
        })->filter()->all();

        $this->permissions()->sync($permIds);
        $this->load('permissions');
    }
}
