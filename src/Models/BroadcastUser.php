<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastSubscriptionStatus;

/**
 * @property int $id
 * @property int $user_id
 * @property BroadcastChannel $channel
 * @property BroadcastSubscriptionStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|BroadcastUser subscribed()
 * @method static Builder<static>|BroadcastUser unsubscribed()
 */
class BroadcastUser extends Model
{
    protected $table = 'broadcast_user';

    protected $fillable = [
        'user_id',
        'channel',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'channel' => BroadcastChannel::class,
            'status' => BroadcastSubscriptionStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('status', BroadcastSubscriptionStatus::Subscribed);
    }

    public function scopeUnsubscribed(Builder $query): Builder
    {
        return $query->where('status', BroadcastSubscriptionStatus::Unsubscribed);
    }
}
