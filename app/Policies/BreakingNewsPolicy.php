<?php

namespace App\Policies;

use App\Models\BreakingNews;
use App\Models\User;

class BreakingNewsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('breaking_news.view');
    }

    public function view(User $user, BreakingNews $breaking): bool
    {
        return $user->hasPermission('breaking_news.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('breaking_news.create');
    }

    public function update(User $user, BreakingNews $breaking): bool
    {
        return $user->hasPermission('breaking_news.update');
    }

    public function delete(User $user, BreakingNews $breaking): bool
    {
        return $user->hasPermission('breaking_news.delete');
    }

    public function publish(User $user, BreakingNews $breaking): bool
    {
        return $user->hasPermission('breaking_news.publish');
    }
}
