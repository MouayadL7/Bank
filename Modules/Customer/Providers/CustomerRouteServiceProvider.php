<?php

namespace Modules\Customer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class CustomerRouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->getNamespace())
                ->group($this->getModulePath('Http/routes.php'));
        });
    }

    /**
     * Configure the module's route middleware.
     *
     * @return void
     */
    protected function mapRoutes()
    {
        // This method is intentionally left empty as routes are mapped in the boot method
        // using the closure-based approach for better flexibility and clarity.
    }

    /**
     * Get the route namespace for the module.
     *
     * @return string|null
     */
    protected function getNamespace()
    {
        return 'Modules\\Customer\\Http\\Controllers';
    }

    /**
     * Get the path to a file or directory within the module.
     *
     * @param string $path
     * @return string
     */
    protected function getModulePath(string $path = ''): string
    {
        $moduleName = str_replace('RouteServiceProvider', '', class_basename($this));
        return base_path('Modules/' . $moduleName . ($path ? '/' . $path : ''));
    }
}
