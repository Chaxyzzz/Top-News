<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('navigation.view');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->hasPermission('navigation.view');
    }

    public function manage(User $user, Menu $menu): bool
    {
        return $user->hasPermission('navigation.manage');
    }
}
