<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastSubscriptionStatus;
use Panelis\Broadcast\Models\BroadcastUser;

if (! function_exists('broadcast_subscribe')) {
    /**
     * Subscribe a user to a given broadcast channel.
     *
     * @param  BroadcastChannel|string  $channel  'database' | 'mail'
     */
    function broadcast_subscribe(BroadcastChannel|string $channel, Model $user): BroadcastUser
    {
        $channel = $channel instanceof BroadcastChannel ? $channel->value : $channel;

        return BroadcastUser::query()->updateOrCreate(
            ['user_id' => $user->getKey(), 'channel' => $channel],
            ['status' => BroadcastSubscriptionStatus::Subscribed->value],
        );
    }
}

if (! function_exists('broadcast_unsubscribe')) {
    /**
     * Unsubscribe a user from a given broadcast channel.
     *
     * @param  BroadcastChannel|string  $channel  'database' | 'mail'
     */
    function broadcast_unsubscribe(BroadcastChannel|string $channel, Model $user): BroadcastUser
    {
        $channel = $channel instanceof BroadcastChannel ? $channel->value : $channel;

        return BroadcastUser::query()->updateOrCreate(
            ['user_id' => $user->getKey(), 'channel' => $channel],
            ['status' => BroadcastSubscriptionStatus::Unsubscribed->value],
        );
    }
}

if (! function_exists('broadcast_is_subscribed')) {
    /**
     * Check whether a user is still subscribed to a given broadcast channel.
     * Users without any subscription record are considered subscribed by default.
     *
     * @param  BroadcastChannel|string  $channel  'database' | 'mail'
     */
    function broadcast_is_subscribed(BroadcastChannel|string $channel, Model $user): bool
    {
        $channel = $channel instanceof BroadcastChannel ? $channel->value : $channel;

        $subscription = BroadcastUser::query()
            ->where('user_id', $user->getKey())
            ->where('channel', $channel)
            ->first();

        return $subscription === null
            || $subscription->status === BroadcastSubscriptionStatus::Subscribed;
    }
}
