<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $modulesPath = base_path('Modules');
        $moduleDirs = scandir($modulesPath);

        foreach ($moduleDirs as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $providerClass = "Modules\\$module\\Providers\\{$module}ServiceProvider";

            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
