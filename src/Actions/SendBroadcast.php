<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Actions;

use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Notifications\BroadcastNotification;

/**
 * Mengirim broadcast ke user terpilih dan/atau seluruh user pada role
 * terpilih (atau semua user jika tidak ada penerima), lalu menandai terkirim.
 */
class SendBroadcast
{
    use AsAction;

    public function handle(Broadcast $broadcast, bool $force = false): void
    {
        if (! $force && ($broadcast->isSent() || $broadcast->isScheduled())) {
            return;
        }

        // Ambil langsung dari query relasi agar tidak memakai relasi yang
        // sudah ter-cache (mis. setelah roles()/users()->sync()).
        $roleIds = $broadcast->roles()->allRelatedIds()->all();
        $userIds = $broadcast->users()->allRelatedIds()->all();

        $userModel = config('auth.providers.users.model');

        $query = $userModel::query();

        $query->when(filled($roleIds) || filled($userIds), function (Builder $query) use ($roleIds, $userIds): void {
            $query->where(function (Builder $query) use ($roleIds, $userIds): void {
                if (filled($roleIds)) {
                    $query->orWhereHas(
                        'roles',
                        fn (Builder $builder): Builder => $builder->whereIn('id', $roleIds)
                    );
                }

                if (filled($userIds)) {
                    $query->orWhereIn('id', $userIds);
                }
            });
        });

        $query->chunk(200, function ($users) use ($broadcast): void {
            foreach ($users as $user) {
                $user->notify(new BroadcastNotification($broadcast));
            }
        });

        $broadcast->update([
            'sent_at' => now(),
            'status' => BroadcastStatus::Sent,
        ]);
    }
}
