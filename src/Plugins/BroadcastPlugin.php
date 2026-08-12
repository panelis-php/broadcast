<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Panelis\Broadcast\Panel\Resources\BroadcastResource;

class BroadcastPlugin implements Plugin
{
    public function getId(): string
    {
        return 'broadcast';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            BroadcastResource::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
