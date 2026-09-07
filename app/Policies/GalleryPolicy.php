<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;

class GalleryPolicy
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

    /**
     * Determine whether the user can view galleries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('galleries.view')
            || $user->hasPermission('articles.create')
            || $user->hasPermission('articles.view_all');
    }

    /**
     * Determine whether the user can view a specific gallery.
     */
    public function view(User $user, Gallery $gallery): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create galleries.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('galleries.create')
            || $user->hasPermission('articles.create');
    }

    /**
     * Determine whether the user can update the gallery.
     */
    public function update(User $user, Gallery $gallery): bool
    {
        if ($user->hasPermission('galleries.update') || $user->hasPermission('articles.update_all')) {
            return true;
        }

        return $gallery->author_id === $user->id;
    }

    /**
     * Determine whether the user can delete the gallery.
     */
    public function delete(User $user, Gallery $gallery): bool
    {
        if ($user->hasPermission('galleries.delete') || $user->hasPermission('articles.delete_all')) {
            return true;
        }

        return $gallery->author_id === $user->id;
    }
}
