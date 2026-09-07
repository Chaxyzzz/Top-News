<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactMessageReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contactMessage) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contact_message',
            'title' => 'Pesan Baru dari Pembaca',
            'message' => "{$this->contactMessage->name} mengirim pesan melalui halaman Kontak.",
            'sender_name' => $this->contactMessage->name,
            'sender_email' => $this->contactMessage->email,
            'subject' => $this->contactMessage->subject,
            'target_url' => route('admin.contacts.show', $this->contactMessage->id),
            'icon' => 'mail',
        ];
    }
}
