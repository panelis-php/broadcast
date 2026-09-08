<?php

declare(strict_types=1);

namespace Panelis\Broadcast\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Panelis\Broadcast\Commands\SendScheduledBroadcasts;

class BroadcastServiceProvider extends ServiceProvider
{
    private const string NAMESPACE = 'broadcast';

    public function register(): void {}

    public function boot(): void
    {
        $this->syncActivityLoggingSetting();

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', self::NAMESPACE);

        $this->loadViewsFrom(__DIR__.'/../../resources/views', self::NAMESPACE);

        Route::middleware('web')->group(__DIR__.'/../../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendScheduledBroadcasts::class,
            ]);
        }
    }

    private function syncActivityLoggingSetting(): void
    {
        $settingClass = 'Panelis\\Setting\\Models\\Setting';

        if (class_exists($settingClass) && config()->has('activitylog.enabled')) {
            config()->set('activitylog.enabled', $settingClass::get('activity.enabled', config('activitylog.enabled')));
        }
    }
}
