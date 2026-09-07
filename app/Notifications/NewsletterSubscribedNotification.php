<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewsletterSubscribedNotification extends Notification
{
    use Queueable;

    public function __construct(public NewsletterSubscriber $subscriber) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'newsletter_subscriber',
            'title' => 'Pelanggan Newsletter Baru',
            'message' => "{$this->subscriber->email} mendaftar sebagai berlangganan berita.",
            'target_url' => route('admin.newsletter.index'),
            'icon' => 'user-plus',
        ];
    }
}
