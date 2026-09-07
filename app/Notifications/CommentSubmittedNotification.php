<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $articleTitle = $this->comment->article?->title ?: 'Artikel';

        return [
            'type' => 'comment_moderation',
            'title' => 'Komentar Baru Menunggu Moderasi',
            'message' => "{$this->comment->author_name} berkomentar pada \"{$articleTitle}\".",
            'comment_id' => $this->comment->id,
            'article_title' => $articleTitle,
            'target_url' => route('admin.comments.index', ['status' => 'pending']),
            'icon' => 'chat',
        ];
    }
}
