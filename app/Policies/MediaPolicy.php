<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;

class MediaPolicy
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
     * Determine whether the user can view the media library.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('media.view')
            || $user->hasPermission('articles.create')
            || $user->hasPermission('articles.update_own');
    }

    /**
     * Determine whether the user can view a specific media item.
     */
    public function view(User $user, Media $media): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can upload media.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('media.upload')
            || $user->hasPermission('articles.create')
            || $user->hasPermission('articles.update_own');
    }

    /**
     * Determine whether the user can update media metadata.
     */
    public function update(User $user, Media $media): bool
    {
        if ($user->hasPermission('media.update') || $user->hasPermission('media.manage')) {
            return true;
        }

        // Author editing own uploaded media
        return $media->uploaded_by === $user->id;
    }

    /**
     * Determine whether the user can delete the media.
     */
    public function delete(User $user, Media $media): bool
    {
        if ($user->hasPermission('media.delete') || $user->hasPermission('media.manage')) {
            return true;
        }

        // Author deleting own unused media
        if ($media->uploaded_by === $user->id && ! $media->isUsed()) {
            return true;
        }

        return false;
    }
}
