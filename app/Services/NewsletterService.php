<?php

namespace App\Services;

use App\Enums\NewsletterSubscriberStatus;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterService
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Subscribe an email address to the newsletter.
     *
     * @return array{status: string, message: string}
     */
    public function subscribe(string $email, ?string $name = null, string $source = 'homepage'): array
    {
        $email = Str::lower(trim($email));
        $subscriber = NewsletterSubscriber::where('email', $email)->first();
        $doubleOptIn = (bool) $this->settings->get('newsletter.double_opt_in', true);

        if ($subscriber) {
            if ($subscriber->status === NewsletterSubscriberStatus::Active) {
                return [
                    'status' => 'already_subscribed',
                    'message' => 'Email Anda telah terdaftar sebagai pelanggan buletin TopNews.',
                ];
            }

            if ($subscriber->status === NewsletterSubscriberStatus::Blocked) {
                return [
                    'status' => 'blocked',
                    'message' => 'Permintaan langganan tidak dapat diproses untuk alamat email ini.',
                ];
            }

            // Unsubscribed or Pending -> Reactivate or Re-verify
            $plainToken = Str::random(40);
            $tokenHash = hash('sha256', $plainToken);

            if ($doubleOptIn) {
                $subscriber->update([
                    'name' => $name ?: $subscriber->name,
                    'status' => NewsletterSubscriberStatus::Pending,
                    'verification_token_hash' => $tokenHash,
                    'source' => $source,
                ]);

                $this->sendVerificationEmail($subscriber, $plainToken);

                return [
                    'status' => 'verification_sent',
                    'message' => 'Periksa email Anda untuk mengonfirmasi pendaftaran buletin TopNews.',
                ];
            }

            $subscriber->update([
                'name' => $name ?: $subscriber->name,
                'status' => NewsletterSubscriberStatus::Active,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'source' => $source,
            ]);

            return [
                'status' => 'subscribed',
                'message' => 'Langganan buletin TopNews Anda telah aktif kembali.',
            ];
        }

        // New Subscriber
        $plainToken = Str::random(40);
        $tokenHash = hash('sha256', $plainToken);

        if ($doubleOptIn) {
            $newSub = NewsletterSubscriber::create([
                'email' => $email,
                'name' => $name,
                'status' => NewsletterSubscriberStatus::Pending,
                'verification_token_hash' => $tokenHash,
                'source' => $source,
            ]);

            $this->sendVerificationEmail($newSub, $plainToken);

            return [
                'status' => 'verification_sent',
                'message' => 'Periksa email Anda untuk mengonfirmasi langganan buletin TopNews.',
            ];
        }

        NewsletterSubscriber::create([
            'email' => $email,
            'name' => $name,
            'status' => NewsletterSubscriberStatus::Active,
            'verified_at' => now(),
            'source' => $source,
        ]);

        return [
            'status' => 'subscribed',
            'message' => 'Terima kasih! Anda telah berhasil berlangganan buletin TopNews.',
        ];
    }

    /**
     * Verify a subscriber using their token.
     *
     * @return array{status: string, message: string}
     */
    public function verify(string $token): array
    {
        $tokenHash = hash('sha256', $token);

        $subscriber = NewsletterSubscriber::where('verification_token_hash', $tokenHash)
            ->where('status', NewsletterSubscriberStatus::Pending)
            ->first();

        if (! $subscriber) {
            return [
                'status' => 'invalid',
                'message' => 'Tautan verifikasi tidak valid atau telah kedaluwarsa.',
            ];
        }

        $subscriber->update([
            'status' => NewsletterSubscriberStatus::Active,
            'verified_at' => now(),
            'verification_token_hash' => null,
        ]);

        return [
            'status' => 'verified',
            'message' => 'Email Anda berhasil diverifikasi! Selamat menikmati buletin harian TopNews.',
        ];
    }

    /**
     * Unsubscribe a subscriber using their secure uuid.
     *
     * @return array{status: string, message: string}
     */
    public function unsubscribe(string $uuid): array
    {
        $subscriber = NewsletterSubscriber::where('uuid', $uuid)->first();

        if (! $subscriber) {
            return [
                'status' => 'not_found',
                'message' => 'Data langganan tidak ditemukan.',
            ];
        }

        $subscriber->update([
            'status' => NewsletterSubscriberStatus::Unsubscribed,
            'unsubscribed_at' => now(),
        ]);

        return [
            'status' => 'unsubscribed',
            'message' => 'Anda telah berhasil berhenti berlangganan buletin TopNews.',
        ];
    }

    /**
     * Block a subscriber.
     */
    public function block(NewsletterSubscriber $subscriber, ?User $actor = null): void
    {
        $subscriber->update(['status' => NewsletterSubscriberStatus::Blocked]);

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log('newsletter.subscriber_blocked', $actor, ['email' => $subscriber->email]);
        }
    }

    /**
     * Reactivate a subscriber.
     */
    public function reactivate(NewsletterSubscriber $subscriber, ?User $actor = null): void
    {
        $subscriber->update([
            'status' => NewsletterSubscriberStatus::Active,
            'unsubscribed_at' => null,
        ]);

        if ($actor && class_exists(AuditLogger::class)) {
            AuditLogger::log('newsletter.subscriber_reactivated', $actor, ['email' => $subscriber->email]);
        }
    }

    /**
     * Export subscribers query to CSV with formula-injection sanitization.
     */
    public function exportCsv(Builder $query): string
    {
        $subscribers = $query->orderBy('created_at', 'desc')->get();
        $output = fopen('php://temp', 'r+');

        // Header row
        fputcsv($output, ['Email', 'Nama', 'Status', 'Sumber', 'Tanggal Daftar', 'Tanggal Verifikasi']);

        foreach ($subscribers as $sub) {
            fputcsv($output, [
                $this->sanitizeCsvValue($sub->email),
                $this->sanitizeCsvValue($sub->name ?? '-'),
                $sub->status->label(),
                $this->sanitizeCsvValue($sub->source ?? 'homepage'),
                $sub->subscribed_at?->format('Y-m-d H:i:s'),
                $sub->verified_at?->format('Y-m-d H:i:s') ?? '-',
            ]);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }

    /**
     * Sanitize cell value to prevent spreadsheet formula injection.
     */
    protected function sanitizeCsvValue(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $firstChar = substr($value, 0, 1);
        if (in_array($firstChar, ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * Send verification email safely.
     */
    protected function sendVerificationEmail(NewsletterSubscriber $subscriber, string $plainToken): void
    {
        try {
            $verifyUrl = route('newsletter.verify', $plainToken);

            // Log or send mail
            Log::info("Newsletter verification link for {$subscriber->email}: {$verifyUrl}");
        } catch (\Throwable $e) {
            Log::error('Failed sending newsletter verification email: '.$e->getMessage());
        }
    }
}
