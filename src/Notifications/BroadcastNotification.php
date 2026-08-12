<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Models\Broadcast;

/**
 * Notifikasi broadcast yang dikirim ke penerima lewat kanal
 * database (bell notifikasi) dan/atau mail.
 */
class BroadcastNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Broadcast $broadcast,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = $this->broadcast->channels ?: [BroadcastChannel::Database->value];

        return array_values(array_intersect(['mail', 'database'], $channels));
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->broadcast->title)
            ->markdown('broadcast::mail.broadcast', [
                'broadcast' => $this->broadcast,
            ]);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->broadcast->title,
            'body' => $this->broadcast->body,
            'type' => $this->broadcast->type->value,
        ];
    }
}
