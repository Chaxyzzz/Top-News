<?php

namespace App\Policies;

use App\Models\AdCampaign;
use App\Models\User;

class AdCampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function view(User $user, AdCampaign $campaign): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ads.create');
    }

    public function update(User $user, AdCampaign $campaign): bool
    {
        return $user->hasPermission('ads.update');
    }

    public function delete(User $user, AdCampaign $campaign): bool
    {
        return $user->hasPermission('ads.delete');
    }
}
