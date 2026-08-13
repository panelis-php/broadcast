<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Actions;

use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Enums\BroadcastSubscriptionStatus;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Models\BroadcastUser;
use Panelis\Broadcast\Notifications\BroadcastNotification;

/**
 * Send a broadcast to selected users and/or all users on the selected roles
 * (or to all users when there are no recipients), then mark it as sent.
 */
class SendBroadcast
{
    use AsAction;

    public function handle(Broadcast $broadcast, bool $force = false): void
    {
        if (! $force && ($broadcast->isSent() || $broadcast->isScheduled())) {
            return;
        }

        // Resolve recipients straight from the relationship queries so we do not
        // use already-cached relations (e.g. after roles()/users()->sync()).
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
                // Ensure a subscription row exists for every broadcast channel.
                // If missing, create one with the default subscribed status.
                foreach ($broadcast->channels ?: [BroadcastChannel::Database->value] as $channel) {
                    BroadcastUser::query()->firstOrCreate(
                        ['user_id' => $user->getKey(), 'channel' => $channel],
                        ['status' => BroadcastSubscriptionStatus::Subscribed->value],
                    );
                }

                $user->notify(new BroadcastNotification($broadcast));
            }
        });

        $broadcast->update([
            'sent_at' => now(),
            'status' => BroadcastStatus::Sent,
        ]);
    }
}
