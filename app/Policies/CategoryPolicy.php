<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Super admin bypass.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermission('categories.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('categories.create');
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasPermission('categories.update');
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasPermission('categories.delete');
    }
}
