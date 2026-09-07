<?php

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    /**
     * Determine whether the user can view settings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('settings.view');
    }

    /**
     * Determine whether the user can update general settings.
     */
    public function update(User $user): bool
    {
        return $user->hasPermission('settings.update');
    }

    /**
     * Determine whether the user can manage branding settings.
     */
    public function branding(User $user): bool
    {
        return $user->hasPermission('settings.branding') || $user->hasPermission('settings.update');
    }

    /**
     * Determine whether the user can manage editorial settings.
     */
    public function editorial(User $user): bool
    {
        return $user->hasPermission('settings.editorial') || $user->hasPermission('settings.update');
    }

    /**
     * Determine whether the user can manage SEO default settings.
     */
    public function seo(User $user): bool
    {
        return $user->hasPermission('settings.seo') || $user->hasPermission('settings.update');
    }
}
