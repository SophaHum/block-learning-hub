<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

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
        // Register the Spatie Permission middleware
        app()['router']->aliasMiddleware('role', RoleMiddleware::class);
        app()['router']->aliasMiddleware('permission', PermissionMiddleware::class);
        app()['router']->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }
}
