<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Models\Article;
use App\Models\ArticleReaction;
use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ArticleEngagementService
{
    /**
     * Toggle bookmark state for the user on a published article.
     *
     * @return array{bookmarked: bool, message: string}
     */
    public function toggleBookmark(User $user, Article $article): array
    {
        if (! $article->isPublished()) {
            throw new InvalidArgumentException('Hanya artikel yang telah dipublikasikan yang dapat disimpan.');
        }

        $existing = Bookmark::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return [
                'bookmarked' => false,
                'message' => 'Artikel dihapus dari simpanan.',
            ];
        }

        Bookmark::create([
            'user_id' => $user->id,
            'article_id' => $article->id,
            'created_at' => now(),
        ]);

        return [
            'bookmarked' => true,
            'message' => 'Artikel berhasil disimpan ke daftar bacaan Anda.',
        ];
    }

    /**
     * Toggle or update user reaction on a published article.
     *
     * @return array{reacted: bool, current_reaction: ?string, counts: array<string, int>}
     */
    public function toggleReaction(User $user, Article $article, ReactionType $reactionType): array
    {
        if (! $article->isPublished()) {
            throw new InvalidArgumentException('Hanya artikel yang telah dipublikasikan yang dapat diberi respon.');
        }

        $existing = ArticleReaction::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existing && $existing->reaction_type === $reactionType) {
            // User clicked the same active reaction -> remove it
            $existing->delete();
            $current = null;
            $reacted = false;
        } elseif ($existing) {
            // User clicked a different reaction -> update it
            $existing->update(['reaction_type' => $reactionType]);
            $current = $reactionType->value;
            $reacted = true;
        } else {
            // User has no reaction yet -> create it
            ArticleReaction::create([
                'article_id' => $article->id,
                'user_id' => $user->id,
                'reaction_type' => $reactionType,
            ]);
            $current = $reactionType->value;
            $reacted = true;
        }

        return [
            'reacted' => $reacted,
            'current_reaction' => $current,
            'counts' => $article->getReactionCounts(),
        ];
    }

    /**
     * Get aggregate engagement metrics for admin overview.
     *
     * @return array<string, mixed>
     */
    public function getAdminOverview(): array
    {
        $approvedComments = DB::table('comments')->where('status', 'approved')->whereNull('deleted_at')->count();
        $pendingComments = DB::table('comments')->where('status', 'pending')->whereNull('deleted_at')->count();
        $totalBookmarks = DB::table('bookmarks')->count();
        $totalReactions = DB::table('article_reactions')->count();

        $mostBookmarked = Article::query()
            ->published()
            ->withCount('bookmarks')
            ->orderBy('bookmarks_count', 'desc')
            ->limit(5)
            ->get();

        $mostCommented = Article::query()
            ->published()
            ->withCount(['comments' => fn ($q) => $q->approved()])
            ->orderBy('comments_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'approved_comments' => $approvedComments,
            'pending_comments' => $pendingComments,
            'total_bookmarks' => $totalBookmarks,
            'total_reactions' => $totalReactions,
            'most_bookmarked' => $mostBookmarked,
            'most_commented' => $mostCommented,
        ];
    }
}
