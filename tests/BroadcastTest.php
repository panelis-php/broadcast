<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Panelis\Broadcast\Actions\SendBroadcast;
use Panelis\Broadcast\Enums\BroadcastChannel;
use Panelis\Broadcast\Enums\BroadcastStatus;
use Panelis\Broadcast\Enums\BroadcastType;
use Panelis\Broadcast\Models\Broadcast;
use Panelis\Broadcast\Notifications\BroadcastNotification;
use Spatie\Permission\Models\Role;

/**
 * User with the default role (following the host application's user model).
 */
function createBroadcastUser(array $attributes = []): Model
{
    $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

    $userModel = config('auth.providers.users.model');

    $user = $userModel::factory()->create($attributes);

    $user->assignRole($role);

    return $user;
}

/**
 * @param  array<string, mixed>  $overrides
 */
function createBroadcast(array $overrides = []): Broadcast
{
    return Broadcast::create(array_merge([
        'title' => 'Hello runners',
        'body' => '**Welcome** to the season.',
        'type' => BroadcastType::Info,
        'status' => BroadcastStatus::Draft,
        'channels' => [BroadcastChannel::Database->value],
    ], $overrides));
}

test('broadcast notifies users on the selected role only', function (): void {
    Notification::fake();

    $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

    $member = createBroadcastUser();
    $outsider = config('auth.providers.users.model')::factory()->create();

    $broadcast = createBroadcast();
    $broadcast->roles()->attach($role);

    SendBroadcast::run($broadcast);

    Notification::assertSentTo($member, BroadcastNotification::class);
    Notification::assertNotSentTo($outsider, BroadcastNotification::class);

    expect($broadcast->fresh()->isSent())->toBeTrue();
});

test('broadcast notifies explicitly selected users only', function (): void {
    Notification::fake();

    $selected = config('auth.providers.users.model')::factory()->create();
    $other = config('auth.providers.users.model')::factory()->create();

    $broadcast = createBroadcast();
    $broadcast->users()->attach($selected);

    SendBroadcast::run($broadcast);

    Notification::assertSentTo($selected, BroadcastNotification::class);
    Notification::assertNotSentTo($other, BroadcastNotification::class);

    expect($broadcast->fresh()->isSent())->toBeTrue();
});

test('broadcast with roles and users sends to the union without duplicates', function (): void {
    Notification::fake();

    $role = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

    $byRole = createBroadcastUser();
    $byUser = config('auth.providers.users.model')::factory()->create();
    $outsider = config('auth.providers.users.model')::factory()->create();

    $broadcast = createBroadcast();
    $broadcast->roles()->attach($role);
    $broadcast->users()->attach($byUser);

    SendBroadcast::run($broadcast);

    Notification::assertSentTo($byRole, BroadcastNotification::class);
    Notification::assertSentTo($byUser, BroadcastNotification::class);
    Notification::assertNotSentTo($outsider, BroadcastNotification::class);

    expect($broadcast->fresh()->isSent())->toBeTrue();
});

test('broadcast with no recipients goes to all users', function (): void {
    Notification::fake();

    $userA = createBroadcastUser();
    $userB = config('auth.providers.users.model')::factory()->create();

    $broadcast = createBroadcast();

    SendBroadcast::run($broadcast);

    Notification::assertSentTo($userA, BroadcastNotification::class);
    Notification::assertSentTo($userB, BroadcastNotification::class);
});

test('broadcast stores markdown body and severity for the frontend', function (): void {
    $broadcast = createBroadcast(['body' => '# Big news', 'type' => BroadcastType::Warning]);

    expect($broadcast->title)->toBe('Hello runners')
        ->and($broadcast->type)->toBe(BroadcastType::Warning)
        ->and($broadcast->channels)->toBe([BroadcastChannel::Database->value]);
});

test('scheduled broadcast is not sent early and the command sends it when due', function (): void {
    Notification::fake();

    $user = createBroadcastUser();
    $role = $user->roles->first();

    $future = createBroadcast([
        'status' => BroadcastStatus::Scheduled,
        'send_at' => now()->addHour(),
    ]);
    $future->roles()->attach($role);

    $due = createBroadcast([
        'status' => BroadcastStatus::Scheduled,
        'send_at' => now()->subMinute(),
    ]);
    $due->roles()->attach($role);

    SendBroadcast::run($future);

    Notification::assertNothingSent();

    $this->artisan('broadcast:send-scheduled')->assertSuccessful();

    Notification::assertSentTo($user, BroadcastNotification::class);

    expect($future->fresh()->isSent())->toBeFalse()
        ->and($due->fresh()->isSent())->toBeTrue();
});

test('draft broadcasts are ignored by the scheduler', function (): void {
    Notification::fake();

    $draft = createBroadcast([
        'status' => BroadcastStatus::Draft,
        'send_at' => now()->subMinute(),
    ]);

    $this->artisan('broadcast:send-scheduled')->assertSuccessful();

    Notification::assertNothingSent();

    expect($draft->fresh()->isDraft())->toBeTrue()
        ->and($draft->fresh()->isSent())->toBeFalse();
});

test('sending an already sent broadcast is a no-op', function (): void {
    Notification::fake();

    $user = createBroadcastUser();
    $role = $user->roles->first();

    $broadcast = createBroadcast([
        'status' => BroadcastStatus::Sent,
        'sent_at' => now(),
    ]);
    $broadcast->roles()->attach($role);

    SendBroadcast::run($broadcast);

    Notification::assertNothingSent();
});

test('resending an already sent broadcast with force works', function (): void {
    Notification::fake();

    $user = createBroadcastUser();
    $role = $user->roles->first();

    $broadcast = createBroadcast([
        'status' => BroadcastStatus::Sent,
        'sent_at' => now()->subDay(),
    ]);
    $broadcast->roles()->attach($role);

    SendBroadcast::run($broadcast, force: true);

    Notification::assertSentTo($user, BroadcastNotification::class);

    expect($broadcast->fresh()->isSent())->toBeTrue()
        ->and($broadcast->fresh()->sent_at->isToday())->toBeTrue();
});
