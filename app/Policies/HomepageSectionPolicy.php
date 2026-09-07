<?php

namespace App\Policies;

use App\Models\HomepageSection;
use App\Models\User;

class HomepageSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('homepage.view');
    }

    public function view(User $user, HomepageSection $section): bool
    {
        return $user->hasPermission('homepage.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('homepage.manage');
    }

    public function update(User $user, HomepageSection $section): bool
    {
        return $user->hasPermission('homepage.manage');
    }

    public function curate(User $user, HomepageSection $section): bool
    {
        return $user->hasPermission('homepage.curate') || $user->hasPermission('homepage.manage');
    }

    public function delete(User $user, HomepageSection $section): bool
    {
        // Protected sections like Hero and Latest cannot be deleted
        if ($section->section_type->isProtected()) {
            return false;
        }

        return $user->hasPermission('homepage.manage');
    }
}
