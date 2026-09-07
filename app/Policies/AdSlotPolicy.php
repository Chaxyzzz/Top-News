<?php

namespace App\Policies;

use App\Models\AdSlot;
use App\Models\User;

class AdSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function view(User $user, AdSlot $slot): bool
    {
        return $user->hasPermission('ads.view');
    }

    public function update(User $user, AdSlot $slot): bool
    {
        return $user->hasPermission('ads.manage');
    }
}
