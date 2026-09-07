<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view any pages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('pages.view');
    }

    /**
     * Determine whether the user can view the page.
     */
    public function view(User $user, Page $page): bool
    {
        return $user->hasPermission('pages.view');
    }

    /**
     * Determine whether the user can create pages.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('pages.create');
    }

    /**
     * Determine whether the user can update the page.
     */
    public function update(User $user, Page $page): bool
    {
        return $user->hasPermission('pages.update');
    }

    /**
     * Determine whether the user can publish or unpublish the page.
     */
    public function publish(User $user, Page $page): bool
    {
        return $user->hasPermission('pages.publish');
    }

    /**
     * Determine whether the user can delete the page.
     * Core institutional pages can NEVER be deleted.
     */
    public function delete(User $user, Page $page): bool
    {
        if ($page->isProtectedCorePage()) {
            return false;
        }

        return $user->hasPermission('pages.delete');
    }
}
