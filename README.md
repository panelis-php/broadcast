# Broadcast

Broadcast notifications (database & mail) to users by role for [Panelis](https://github.com/panelis-php).

## Features

- **List broadcasts** — Filament table with the history of what was sent.
- **New broadcast** — send a notification (database bell / email) to users by role, or everyone.
- **Edit / delete drafts** — broadcasts still in `draft` status can be edited or deleted; once scheduled or sent they are locked.
- **Email unsubscribe** — broadcast emails include an unsubscribe link (signed URL, no login required) that opts the user out of the `mail` channel. Users without any subscription record are considered subscribed by default.

## Email subscriptions

The `broadcast_user` table tracks per-user subscription state per channel
(`database` | `mail`) with a `status` of `subscribed` | `unsubscribed`.

- The table starts empty — users without a record are treated as subscribed.
- When a broadcast is sent, missing subscription rows are created with `subscribed` as the default.
- Unsubscribed users are skipped for that channel.

Helpers for registered users (e.g. notification settings page in the host app):

```php
use Panelis\Broadcast\Enums\BroadcastChannel;

// Subscribe / unsubscribe from a given channel
broadcast_subscribe(BroadcastChannel::Mail, $user);
broadcast_unsubscribe(BroadcastChannel::Mail, $user);

// Check subscription status
broadcast_is_subscribed(BroadcastChannel::Mail, $user); // bool
```

## Form fields

- **Title** — the notification title.
- **Recipients** — multiple select based on role (empty = all users).
- **Type** — `info`, `warning`, `success`, `error`.
- **Channel** — `database`, `mail` (multi).
- **Content** — Markdown editor (rendered as markdown in the notification view).
- **Send at** — empty = now, or a future date for scheduling.

## Installation

```bash
composer require panelis-php/broadcast
```

Register the scheduler for scheduled broadcasts (in the host app's `routes/console.php`):

```php
Schedule::command('broadcast:send-scheduled')->everyMinute();
```

Add `Panelis\Broadcast\Panel\Resources\BroadcastResource\Enums\BroadcastPermission::class`
to `config('permission.enums')`, then re-seed permissions from the Permissions page.

## Requirements

- PHP 8.3+
- Laravel 13
- Filament 5
- spatie/laravel-permission

## License

MIT
