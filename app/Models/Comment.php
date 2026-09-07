<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'article_id',
        'user_id',
        'parent_id',
        'body',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'spam_reason',
    ];

    protected $casts = [
        'status' => CommentStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Boot model events.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Comment $comment) {
            if (empty($comment->uuid)) {
                $comment->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Article being commented on.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Author of the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parent comment (if reply).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Nested replies (max depth 1).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
    }

    /**
     * Approved nested replies.
     */
    public function approvedReplies(): HasMany
    {
        return $this->replies()->where('status', CommentStatus::Approved);
    }

    /**
     * Staff member who approved the comment.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Staff member who rejected the comment.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if comment is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === CommentStatus::Approved;
    }

    /**
     * Check if comment is pending moderation.
     */
    public function isPending(): bool
    {
        return $this->status === CommentStatus::Pending;
    }

    /**
     * Scope to approved comments only.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Approved);
    }

    /**
     * Scope to pending comments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Pending);
    }

    /**
     * Scope to top-level comments (parent_id is null).
     */
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
