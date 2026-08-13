<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Models\Broadcast;

/**
 * Broadcast notification sent to recipients via the
 * database (notification bell) and/or mail channels.
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
        $channels = array_values(array_intersect(
            ['mail', 'database'],
            $this->broadcast->channels ?: [BroadcastChannel::Database->value],
        ));

        return array_values(array_filter(
            $channels,
            fn (string $channel): bool => $this->isSubscribed($notifiable, $channel),
        ));
    }

    /**
     * Users without any subscription record are considered subscribed by default.
     */
    private function isSubscribed(object $notifiable, string $channel): bool
    {
        if (! $notifiable instanceof Model) {
            return true;
        }

        return broadcast_is_subscribed($channel, $notifiable);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->broadcast->title)
            ->markdown('broadcast::mail.broadcast', [
                'broadcast' => $this->broadcast,
                'unsubscribeUrl' => URL::signedRoute('broadcast.unsubscribe', [
                    'user' => $notifiable->getKey(),
                ]),
            ]);
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->broadcast->title,
            'body' => $this->broadcast->body,
            'type' => $this->broadcast->type->value,
            'url' => $this->broadcast->url,
            'label' => $this->broadcast->label,
        ];
    }
}
