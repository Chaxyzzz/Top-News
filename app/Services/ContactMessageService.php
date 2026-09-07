<?php

namespace App\Services;

use App\Enums\ContactMessageStatus;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceivedNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ContactMessageService
{
    /**
     * Store an incoming public contact message.
     *
     * @param  array{name: string, email: string, subject: string, message: string, category?: ?string}  $data
     */
    public function storeMessage(array $data, ?string $ip = null, ?string $userAgent = null): ContactMessage
    {
        $ipHash = $ip ? hash_hmac('sha256', $ip, (string) config('app.key')) : null;
        $uaSummary = $userAgent ? Str::limit(trim($userAgent), 150) : null;

        $message = ContactMessage::create([
            'name' => trim($data['name']),
            'email' => Str::lower(trim($data['email'])),
            'subject' => trim($data['subject']),
            'message' => trim($data['message']),
            'category' => $data['category'] ?? null,
            'status' => ContactMessageStatus::New,
            'ip_hash' => $ipHash,
            'user_agent_summary' => $uaSummary,
        ]);

        try {
            $staff = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin']))->get();
            Notification::send($staff, new ContactMessageReceivedNotification($message));
        } catch (\Throwable $e) {
            // Log notification error silently
        }

        return $message;
    }

    /**
     * Mark message as read.
     */
    public function markRead(ContactMessage $message, ?User $actor = null): void
    {
        if ($message->status === ContactMessageStatus::New) {
            $message->update([
                'status' => ContactMessageStatus::Read,
                'read_at' => now(),
            ]);

            if ($actor && class_exists(AuditLogger::class)) {
                AuditLogger::log('contact.read', $actor, ['message_id' => $message->id]);
            }
        }
    }

    /**
     * Update message status.
     */
    public function updateStatus(ContactMessage $message, ContactMessageStatus $status, ?User $actor = null): void
    {
        $payload = ['status' => $status];

        if ($status === ContactMessageStatus::Resolved) {
            $payload['resolved_at'] = now();
        }

        $message->update($payload);

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log('contact.status_updated', $actor, [
                'message_id' => $message->id,
                'status' => $status->value,
            ]);
        }
    }

    /**
     * Assign message to a staff user.
     */
    public function assign(ContactMessage $message, ?User $staff, ?User $actor = null): void
    {
        $message->update(['assigned_to' => $staff?->id]);

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log('contact.assigned', $actor, [
                'message_id' => $message->id,
                'assigned_to' => $staff?->name ?? 'None',
            ]);
        }
    }

    /**
     * Mark message as spam.
     */
    public function markSpam(ContactMessage $message, ?User $actor = null): void
    {
        $message->update(['status' => ContactMessageStatus::Spam]);

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log('contact.marked_spam', $actor, ['message_id' => $message->id]);
        }
    }
}
