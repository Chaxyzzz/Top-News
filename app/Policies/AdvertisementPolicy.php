<?php

namespace App\Policies;

use App\Models\Advertisement;
use App\Models\User;

class AdvertisementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function view(User $user, Advertisement $ad): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ads.create');
    }

    public function update(User $user, Advertisement $ad): bool
    {
        return $user->hasPermission('ads.update');
    }

    public function delete(User $user, Advertisement $ad): bool
    {
        return $user->hasPermission('ads.delete');
    }

    public function analytics(User $user): bool
    {
        return $user->hasPermission('ads.analytics');
    }
}
