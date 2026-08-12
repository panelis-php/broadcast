<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Commands;

use Illuminate\Console\Command;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Models\Broadcast;

/**
 * Mengirim broadcast yang sudah jatuh tempo.
 * Didaftarkan di scheduler aplikasi utama (mis. setiap menit).
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
            ->each(fn(Broadcast $broadcast) => SendBroadcast::run($broadcast));

        return self::SUCCESS;
    }
}
