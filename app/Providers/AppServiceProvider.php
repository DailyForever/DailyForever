<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\SecurityHeaders;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('admin', AdminOnly::class);
        $this->app['router']->pushMiddlewareToGroup('web', SecurityHeaders::class);
    }
}
