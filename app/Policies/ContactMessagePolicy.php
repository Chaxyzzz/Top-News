<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    /**
     * Determine whether the user can view contact messages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('contacts.view');
    }

    /**
     * Determine whether the user can view a specific contact message.
     */
    public function view(User $user, ContactMessage $message): bool
    {
        return $user->hasPermission('contacts.view');
    }

    /**
     * Determine whether the user can manage contact message status.
     */
    public function manage(User $user, ?ContactMessage $message = null): bool
    {
        return $user->hasPermission('contacts.manage');
    }

    /**
     * Determine whether the user can assign contact messages to staff.
     */
    public function assign(User $user, ?ContactMessage $message = null): bool
    {
        return $user->hasPermission('contacts.assign');
    }

    /**
     * Determine whether the user can delete contact messages.
     */
    public function delete(User $user, ContactMessage $message): bool
    {
        return $user->hasPermission('contacts.delete');
    }
}
