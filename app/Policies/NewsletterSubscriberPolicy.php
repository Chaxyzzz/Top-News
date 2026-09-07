<?php

namespace App\Policies;

use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberPolicy
{
    /**
     * Determine whether the user can view any subscribers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('newsletter.view');
    }

    /**
     * Determine whether the user can manage subscriber status (block/reactivate).
     */
    public function manage(User $user, ?NewsletterSubscriber $subscriber = null): bool
    {
        return $user->hasPermission('newsletter.manage');
    }

    /**
     * Determine whether the user can export subscribers data.
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('newsletter.export');
    }
}
