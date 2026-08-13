# Broadcast

Broadcast notifications (database & mail) to users by role for [Panelis](https://github.com/panelis-php).

## Features

- **List broadcasts** — Filament table with the history of what was sent.
- **New broadcast** — send a notification (database bell / email) to users by role, or everyone.
- **Edit / delete drafts** — broadcasts still in `draft` status can be edited or deleted; once scheduled or sent they are locked.

## Form fields

- **Title** — judul notifikasi.
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
