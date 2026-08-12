<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Providers;

use Illuminate\Support\ServiceProvider;
use Panelis\Broadcast\Commands\SendScheduledBroadcasts;

class BroadcastServiceProvider extends ServiceProvider
{
    private const string NAMESPACE = 'broadcast';

    public function register(): void {}

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../../lang', self::NAMESPACE);

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', self::NAMESPACE);

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendScheduledBroadcasts::class,
            ]);
        }
    }
}
