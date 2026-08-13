<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Commands;

use Illuminate\Console\Command;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Models\Broadcast;

/**
 * Send broadcasts that are due.
 * Registered in the host application scheduler (e.g. every minute).
 */
class SendScheduledBroadcasts extends Command
{
    protected $signature = 'broadcast:send-scheduled';

    protected $description = 'Send scheduled broadcasts that are due.';

    public function handle(): int
    {
        Broadcast::query()
            ->pending()
            ->whereNotNull('send_at')
            ->where('send_at', '<=', now())
            ->each(fn (Broadcast $broadcast) => SendBroadcast::run($broadcast));

        return self::SUCCESS;
    }
}
