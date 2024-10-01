<?php

namespace Xchimx\UnsplashApi;

use Illuminate\Support\ServiceProvider;

class UnsplashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/unsplash.php', 'unsplash');

        $this->app->singleton('unsplash', function ($app) {
            return new UnsplashService;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/unsplash.php' => config_path('unsplash.php'),
        ], 'config');

        $this->app['router']->aliasMiddleware('unsplash.rate_limit', \Xchimx\UnsplashApi\Middleware\UnsplashRateLimitMiddleware::class);

        $this->publishes([
            __DIR__.'/Middleware/UnsplashRateLimitMiddleware.php' => app_path('Http/Middleware/UnsplashRateLimitMiddleware.php'),
        ], 'middleware');
    }
}
