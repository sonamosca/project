<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router; // <-- Import Router
use App\Http\Middleware\EnsureUserIsAdmin; // <-- Import your middleware

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        $router->aliasMiddleware('isAdmin', EnsureUserIsAdmin::class);
    }
}
