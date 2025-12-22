<?php

namespace Modules\Dashboard\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class DashboardRouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->getNamespace())
                ->group($this->getModulePath('Http/routes.php'));
        });
    }

    protected function getNamespace(): string
    {
        return 'Modules\\Dashboard\\Http\\Controllers';
    }

    protected function getModulePath(string $path = ''): string
    {
        $moduleName = str_replace('RouteServiceProvider', '', class_basename($this));
        return base_path('Modules/' . $moduleName . ($path ? '/' . $path : ''));
    }
}

