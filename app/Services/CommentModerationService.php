<?php

namespace App\Services;

use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\CommentSubmittedNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class CommentModerationService
{
    public function __construct(
        protected AuditLogService $auditLog
    ) {}

    /**
     * Create a new reader comment or reply (pending moderation by default).
     *
     * @throws ValidationException|InvalidArgumentException
     */
    public function createComment(User $user, Article $article, string $rawBody, ?int $parentId = null): Comment
    {
        if (! $article->isPublished()) {
            throw new InvalidArgumentException('Komentar tidak dapat dikirim untuk artikel yang belum dipublikasikan.');
        }

        if (! $article->allow_comments) {
            throw new InvalidArgumentException('Kolom komentar telah dinonaktifkan untuk artikel ini.');
        }

        if (! $user->isActive()) {
            throw new InvalidArgumentException('Akun Anda tidak aktif atau sedang ditangguhkan.');
        }

        // Rate Limiting: 5 comments per 10 minutes per user
        $rateLimitKey = 'comment-submission:'.$user->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'body' => ["Terlalu banyak komentar dalam waktu singkat. Silakan coba kembali dalam {$seconds} detik."],
            ]);
        }

        // Clean and sanitize body to pure plain text
        $body = trim(strip_tags($rawBody));

        if (mb_strlen($body) < 2) {
            throw ValidationException::withMessages([
                'body' => ['Komentar minimal terdiri dari 2 karakter.'],
            ]);
        }

        if (mb_strlen($body) > 2000) {
            throw ValidationException::withMessages([
                'body' => ['Komentar maksimal 2000 karakter.'],
            ]);
        }

        // Validate parent_id (Max nesting depth = 1)
        if ($parentId) {
            $parent = Comment::find($parentId);

            if (! $parent || $parent->article_id !== $article->id) {
                throw ValidationException::withMessages([
                    'parent_id' => ['Komentar rujukan tidak valid untuk artikel ini.'],
                ]);
            }

            // Replies cannot be nested under another reply
            if ($parent->parent_id !== null) {
                throw ValidationException::withMessages([
                    'parent_id' => ['Balasan komentar dibatasi maksimal 1 tingkat.'],
                ]);
            }
        }

        // Prevent immediate exact duplicate comment
        $recentDuplicate = Comment::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->where('body', $body)
            ->where('created_at', '>=', now()->subMinutes(2))
            ->exists();

        if ($recentDuplicate) {
            throw ValidationException::withMessages([
                'body' => ['Anda baru saja mengirimkan komentar yang sama persis.'],
            ]);
        }

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'body' => $body,
            'status' => CommentStatus::Pending,
        ]);

        RateLimiter::hit($rateLimitKey, 600);

        $this->auditLog->log(
            'comment.submitted',
            $comment,
            "Komentar dikirim oleh {$user->name} pada artikel '{$article->title}'.",
            $user
        );

        try {
            $staff = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin', 'editor']))->get();
            Notification::send($staff, new CommentSubmittedNotification($comment));
        } catch (\Throwable $e) {
            // Log notification error silently
        }

        return $comment;
    }

    /**
     * Approve a pending or rejected comment.
     */
    public function approve(Comment $comment, User $moderator): Comment
    {
        $comment->update([
            'status' => CommentStatus::Approved,
            'approved_by' => $moderator->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'spam_reason' => null,
        ]);

        $this->auditLog->log(
            'comment.approved',
            $comment,
            "Komentar #{$comment->id} disetujui oleh moderator {$moderator->name}.",
            $moderator
        );

        return $comment;
    }

    /**
     * Reject a comment.
     */
    public function reject(Comment $comment, User $moderator, ?string $reason = null): Comment
    {
        $comment->update([
            'status' => CommentStatus::Rejected,
            'rejected_by' => $moderator->id,
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'spam_reason' => $reason,
        ]);

        $this->auditLog->log(
            'comment.rejected',
            $comment,
            "Komentar #{$comment->id} ditolak oleh moderator {$moderator->name}.".($reason ? " Alasan: {$reason}" : ''),
            $moderator
        );

        return $comment;
    }

    /**
     * Mark a comment as spam.
     */
    public function markSpam(Comment $comment, User $moderator, ?string $reason = 'Spam submission'): Comment
    {
        $comment->update([
            'status' => CommentStatus::Spam,
            'rejected_by' => $moderator->id,
            'rejected_at' => now(),
            'approved_by' => null,
            'approved_at' => null,
            'spam_reason' => $reason,
        ]);

        $this->auditLog->log(
            'comment.spammed',
            $comment,
            "Komentar #{$comment->id} ditandai sebagai spam oleh {$moderator->name}.",
            $moderator
        );

        return $comment;
    }

    /**
     * Delete a comment (soft delete).
     */
    public function deleteComment(Comment $comment, User $actor): bool
    {
        $deleted = (bool) DB::transaction(function () use ($comment) {
            Comment::where('parent_id', $comment->id)->forceDelete();

            return $comment->forceDelete();
        });

        $this->auditLog->log(
            'comment.deleted',
            $comment,
            "Komentar #{$comment->id} dihapus permanen oleh {$actor->name}.",
            $actor
        );

        return $deleted;
    }

    /**
     * Get paginated comments for admin moderation.
     */
    public function getModerationList(Request $request, int $perPage = 25): LengthAwarePaginator
    {
        $query = Comment::query()
            ->with(['user', 'article:id,title,slug', 'parent.user'])
            ->latest();

        // Status tab filter
        $status = $request->input('status');
        if ($status && in_array($status, ['pending', 'approved', 'rejected', 'spam'], true)) {
            $query->where('status', $status);
        }

        // Article filter
        if ($request->filled('article_id')) {
            $query->where('article_id', $request->input('article_id'));
        }

        // Search in body, commenter name, or article title
        if ($request->filled('search')) {
            $search = '%'.$request->input('search').'%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('body', 'like', $search)
                    ->orWhereHas('user', function (Builder $u) use ($search) {
                        $u->where('name', 'like', $search)->orWhere('username', 'like', $search);
                    })
                    ->orWhereHas('article', function (Builder $a) use ($search) {
                        $a->where('title', 'like', $search);
                    });
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
