<?php

namespace Modules\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * The module name.
     *
     * @var string
     */
    protected string $name = 'Notification';

    /**
     * The module name in lowercase.
     *
     * @var string
     */
    protected string $nameLower = 'notification';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->loadMigrationsFrom($this->getModulePath('Database/Migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(NotificationEventServiceProvider::class);
        $this->app->register(NotificationRouteServiceProvider::class);
        $this->app->register(NotificationRepositoryServiceProvider::class);
        $this->app->register(NotificationAuthServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
           // NotificationCommand::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // Define your scheduled commands here
    }

    /**
     * Register translations.
     */
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

    /**
     * Register config.
     */
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

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the path to a file or directory within the module.
     *
     * @param string $path
     * @return string
     */
    protected function getModulePath(string $path = ''): string
    {
        return base_path('Modules/' . $this->name . ($path ? '/' . $path : ''));
    }
}
