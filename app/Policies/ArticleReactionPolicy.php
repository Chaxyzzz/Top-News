<?php

namespace App\Policies;

use App\Models\ArticleReaction;
use App\Models\User;

class ArticleReactionPolicy
{
    /**
     * Determine whether the user can create or toggle reactions.
     */
    public function create(User $user): bool
    {
        return $user->isActive();
    }

    /**
     * Determine whether the user can delete their reaction.
     */
    public function delete(User $user, ArticleReaction $reaction): bool
    {
        return $reaction->user_id === $user->id;
    }
}
