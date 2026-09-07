<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
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
     * Determine whether the user can view comment moderation list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('comments.view');
    }

    /**
     * Determine whether the user can view a specific comment in admin.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.view') || $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can submit comments.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Comments cannot be directly edited to prevent moderation bypass.
     */
    public function update(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the comment.
     * Moderator can delete any comment; reader can delete their own comment if pending or approved.
     */
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->hasPermission('comments.delete')) {
            return true;
        }

        return $comment->user_id === $user->id;
    }

    /**
     * Determine whether the user can moderate comments.
     */
    public function moderate(User $user): bool
    {
        return $user->hasPermission('comments.moderate');
    }

    /**
     * Determine whether the user can approve the comment.
     */
    public function approve(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.moderate');
    }

    /**
     * Determine whether the user can reject the comment.
     */
    public function reject(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.moderate');
    }

    /**
     * Determine whether the user can mark comment as spam.
     */
    public function markSpam(User $user, Comment $comment): bool
    {
        return $user->hasPermission('comments.mark_spam');
    }
}
