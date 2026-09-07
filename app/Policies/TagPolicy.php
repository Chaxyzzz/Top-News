<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;

class TagPolicy
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
        return $user->hasPermission('tags.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('tags.create');
    }

    public function update(User $user, Tag $tag): bool
    {
        return $user->hasPermission('tags.update');
    }

    public function delete(User $user, Tag $tag): bool
    {
        return $user->hasPermission('tags.delete');
    }
}
