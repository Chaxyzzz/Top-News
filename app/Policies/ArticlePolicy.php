<?php

namespace App\Policies;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
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
     * Determine whether the user can view any articles in admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('articles.view') || $user->hasPermission('articles.view_all');
    }

    /**
     * Determine whether the user can view the specific article in admin.
     */
    public function view(User $user, Article $article): bool
    {
        if ($user->hasPermission('articles.view_all')) {
            return true;
        }

        if ($article->author_id === $user->id) {
            return true;
        }

        if ($user->hasPermission('articles.review') && in_array($article->status, [
            ArticleStatus::Submitted,
            ArticleStatus::InReview,
            ArticleStatus::RevisionRequested,
            ArticleStatus::Approved,
            ArticleStatus::Scheduled,
        ])) {
            return true;
        }

        return $article->status === ArticleStatus::Published;
    }

    /**
     * Determine whether the user can create articles.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('articles.create');
    }

    /**
     * Determine whether the user can update the article.
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->hasPermission('articles.update_all')) {
            return true;
        }

        // Author editing own article
        if ($article->author_id === $user->id && $user->hasPermission('articles.update_own')) {
            // Authors can edit draft, revision_requested, or submitted articles
            return in_array($article->status, [
                ArticleStatus::Draft,
                ArticleStatus::RevisionRequested,
                ArticleStatus::Submitted,
            ]);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the article.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->hasPermission('articles.delete_all')) {
            return true;
        }

        if ($article->author_id === $user->id && $user->hasPermission('articles.delete_own')) {
            return $article->status === ArticleStatus::Draft;
        }

        return false;
    }

    /**
     * Determine whether the user can submit the article for review.
     */
    public function submit(User $user, Article $article): bool
    {
        if (! $user->hasPermission('articles.submit')) {
            return false;
        }

        return ($article->author_id === $user->id || $user->hasPermission('articles.update_all'))
            && in_array($article->status, [ArticleStatus::Draft, ArticleStatus::RevisionRequested]);
    }

    /**
     * Determine whether the user can start reviewing the article.
     */
    public function startReview(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.review')
            && $article->status === ArticleStatus::Submitted;
    }

    /**
     * Determine whether the user can request a revision.
     */
    public function requestRevision(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.request_revision')
            && in_array($article->status, [ArticleStatus::Submitted, ArticleStatus::InReview]);
    }

    /**
     * Determine whether the user can approve the article.
     */
    public function approve(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.approve')
            && in_array($article->status, [ArticleStatus::Submitted, ArticleStatus::InReview]);
    }

    /**
     * Determine whether the user can publish the article.
     */
    public function publish(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.publish')
            && in_array($article->status, [ArticleStatus::Approved, ArticleStatus::Scheduled]);
    }

    /**
     * Determine whether the user can schedule the article.
     */
    public function schedule(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.schedule')
            && in_array($article->status, [ArticleStatus::Approved, ArticleStatus::Scheduled]);
    }

    /**
     * Determine whether the user can unpublish the article.
     */
    public function unpublish(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.unpublish')
            && $article->status === ArticleStatus::Published;
    }

    /**
     * Determine whether the user can archive the article.
     */
    public function archive(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.archive');
    }

    /**
     * Determine whether the user can restore the article from archive.
     */
    public function restore(User $user, Article $article): bool
    {
        return $user->hasPermission('articles.restore');
    }

    /**
     * Determine whether the user can preview unpublished article.
     */
    public function preview(User $user, Article $article): bool
    {
        return $article->author_id === $user->id
            || $user->hasPermission('articles.review')
            || $user->hasPermission('articles.view_all');
    }

    /**
     * Determine whether the user can reassign the author.
     */
    public function assignAuthor(User $user): bool
    {
        return $user->hasPermission('articles.assign_author') || $user->hasPermission('articles.update_all');
    }
}
