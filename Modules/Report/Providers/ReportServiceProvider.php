<?php

namespace Modules\Report\Providers;

use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ReportServiceProvider extends ServiceProvider
{
    protected string $name = 'Report';
    protected string $nameLower = 'report';

    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->loadMigrationsFrom($this->getModulePath('Database/Migrations'));
    }

    public function register(): void
    {
        $this->app->register(ReportEventServiceProvider::class);
        $this->app->register(ReportRouteServiceProvider::class);
        $this->app->register(ReportRepositoryServiceProvider::class);
    }

    protected function registerCommands(): void
    {
        $this->commands([
            // Add report console commands here.
        ]);
    }

    protected function registerCommandSchedules(): void
    {
        // Define your scheduled commands here.
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom($this->getModulePath('Lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom($this->getModulePath('Lang'));
        }
    }

    protected function registerConfig(): void
    {
        $configPath = $this->getModulePath('Config');

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $relativePath = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $configKey = $this->nameLower . '.' . str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $relativePath);
                    $key = (basename($relativePath) === 'config.php') ? $this->nameLower : $configKey;

                    $this->publishes([$file->getPathname() => config_path($relativePath)], 'config');
                    $this->mergeConfigFrom($file->getPathname(), $key);
                }
            }
        }
    }

    public function provides(): array
    {
        return [];
    }

    protected function getModulePath(string $path = ''): string
    {
        return base_path('Modules/' . $this->name . ($path ? '/' . $path : ''));
    }
}

