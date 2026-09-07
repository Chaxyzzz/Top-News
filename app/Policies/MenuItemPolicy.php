<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;

class MenuItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('navigation.view');
    }

    public function view(User $user, MenuItem $item): bool
    {
        return $user->hasPermission('navigation.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('navigation.manage');
    }

    public function update(User $user, MenuItem $item): bool
    {
        return $user->hasPermission('navigation.manage');
    }

    public function delete(User $user, MenuItem $item): bool
    {
        return $user->hasPermission('navigation.manage');
    }
}
