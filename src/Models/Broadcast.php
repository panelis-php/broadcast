<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Enums\BroadcastType;
use Spatie\Permission\Models\Role;

/**
 * @property int $id
 * @property string $title
 * @property string $body
 * @property BroadcastType $type
 * @property BroadcastStatus $status
 * @property array<int, string> $channels
 * @property string|null $url
 * @property string|null $label
 * @property Collection<int, Role> $roles
 * @property Collection<int, Model> $users
 * @property Carbon|null $send_at
 * @property Carbon|null $sent_at
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Broadcast pending()
 */
class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'type',
        'status',
        'channels',
        'url',
        'label',
        'send_at',
        'sent_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => BroadcastType::class,
            'status' => BroadcastStatus::class,
            'channels' => 'array',
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Recipient roles of the broadcast (many-to-many via broadcast_recipients).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role', Role::class),
            'broadcast_recipients'
        )->wherePivotNotNull('role_id');
    }

    /**
     * Recipient users of the broadcast (many-to-many via broadcast_recipients).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            'broadcast_recipients'
        )->wherePivotNotNull('user_id');
    }

    /**
     * Draft: saved, not yet scheduled or sent.
     */
    public function isDraft(): bool
    {
        return $this->status === BroadcastStatus::Draft;
    }

    /**
     * Scheduled in the future (not yet sent).
     */
    public function isScheduled(): bool
    {
        return $this->status === BroadcastStatus::Scheduled
            && $this->send_at !== null
            && $this->send_at->isFuture();
    }

    public function isSent(): bool
    {
        return $this->status === BroadcastStatus::Sent;
    }

    /**
     * Broadcasts that are scheduled (processed by the scheduler).
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BroadcastStatus::Scheduled);
    }
}
