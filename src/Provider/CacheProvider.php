<?php

namespace Swis\Laravel\StaticRequestCache\Provider;

use Illuminate\Support\ServiceProvider;
use Swis\Laravel\StaticRequestCache\Commands\ClearStaticCache;
use Swis\Laravel\StaticRequestCache\StaticRequestCache;

class CacheProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/static-html-cache.php' => config_path('static-html-cache.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/static-html-cache.php', 'static-html-cache');

        $this->app->singleton(StaticRequestCache::class);

        $this->commands([
            ClearStaticCache::class,
        ]);
    }

    public function provides(): array
    {
        return [
            'static-html-cache'
        ];
    }
}
