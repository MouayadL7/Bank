<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use FilesystemIterator;
use Illuminate\Support\Facades\Artisan;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module structure with all necessary files.';

    /**
     * Get the base path for stubs.
     *
     * @return string
     */
    protected function getStubPath(): string
    {
        return app_path('Console/stubs/module');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $moduleName = Str::studly($name); // PascalCase: Course, BlogPost
        $singularName = Str::singular($moduleName); // Course, User
        $pluralName = Str::plural($moduleName); // Courses, Users
        $snakeCaseSingular = Str::snake($singularName); // course, blog_post
        $snakeCasePlural = Str::snake($pluralName); // courses, blog_posts
        $camelCaseSingular = Str::camel($singularName); // course, blogPost

        $modulePath = base_path("Modules/{$moduleName}");

        if ($this->directoryExistsAndNotEmpty($modulePath)) {
            $this->error("Module '{$moduleName}' already exists and is not empty!");
            return Command::FAILURE;
        }

        $this->info("Creating module: {$moduleName}...");

        // 1. Create base module directory
        mkdir($modulePath, 0755, true);

        // 2. Create subdirectories
        // We'll create all necessary directories here, even if some files are made by other commands,
        // to ensure the basic structure is always present.
        $directories = [
            'Providers',
            'Http/Controllers',
            'Http/Requests',
            'Http/Resources',
            'Http/Middleware',
            'Models',
            'Database/Migrations',
            'Database/Seeders',
            'Database/Factories',
            'Services',
            'Repositories/Interfaces',
            'Repositories/Eloquent',
            'DTOs',
            'Config',
            'Lang/en',
            'Lang/ar',
        ];

        foreach ($directories as $dir) {
            mkdir("{$modulePath}/{$dir}", 0755, true);
        }

        // 3. Generate core files using direct stub replacement (for files without dedicated commands yet)
        // These are files that are essential for the module's basic structure and don't typically
        // require complex, dynamic generation logic beyond simple placeholder replacement.

        // Providers
        $this->createFile("{$modulePath}/Providers/{$moduleName}ServiceProvider.php", $this->replaceModulePlaceholders($this->getStub('service-provider.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Providers/{$moduleName}EventServiceProvider.php", $this->replaceModulePlaceholders($this->getStub('event-service-provider.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Providers/{$moduleName}RouteServiceProvider.php", $this->replaceModulePlaceholders($this->getStub('route-service-provider.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Providers/{$moduleName}RepositoryServiceProvider.php", $this->replaceModulePlaceholders($this->getStub('repository-service-provider.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Providers/{$moduleName}AuthServiceProvider.php", $this->replaceModulePlaceholders($this->getStub('auth-service-provider.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));

        // HTTP
        $this->createFile("{$modulePath}/Http/Controllers/{$moduleName}Controller.php", $this->replaceModulePlaceholders($this->getStub('controller.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Http/Requests/Store{$singularName}Request.php", $this->replaceModulePlaceholders($this->getStub('store-request.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular, 'Store' . $singularName . 'Request'));
        $this->createFile("{$modulePath}/Http/Requests/Update{$singularName}Request.php", $this->replaceModulePlaceholders($this->getStub('update-request.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular, 'Update' . $singularName . 'Request'));
        $this->createFile("{$modulePath}/Http/Resources/{$singularName}Resource.php", $this->replaceModulePlaceholders($this->getStub('resource.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Http/routes.php", $this->replaceModulePlaceholders($this->getStub('routes.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));

        // Models & Factories
        $this->createFile("{$modulePath}/Models/{$singularName}.php", $this->replaceModulePlaceholders($this->getStub('model.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Database/Factories/{$singularName}Factory.php", $this->replaceModulePlaceholders($this->getStub('factory.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Database/Migrations/" . date('Y_m_d_His') . "_create_{$snakeCasePlural}_table.php", $this->replaceModulePlaceholders($this->getStub('migration-create.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular, null, $snakeCasePlural));

        // Services & Repositories
        $this->createFile("{$modulePath}/Services/{$singularName}Service.php", $this->replaceModulePlaceholders($this->getStub('service.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Repositories/Interfaces/{$singularName}RepositoryInterface.php", $this->replaceModulePlaceholders($this->getStub('repository-interface.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Repositories/Eloquent/{$singularName}Repository.php", $this->replaceModulePlaceholders($this->getStub('repository-eloquent.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));

        // DTOs
        $this->createFile("{$modulePath}/DTOs/{$singularName}Data.php", $this->replaceModulePlaceholders($this->getStub('dto.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));

        // Config & Lang
        $this->createFile("{$modulePath}/Config/{$snakeCaseSingular}.php", $this->replaceModulePlaceholders($this->getStub('config.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Lang/en/{$snakeCaseSingular}.php", $this->replaceModulePlaceholders($this->getStub('lang.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));
        $this->createFile("{$modulePath}/Lang/ar/{$snakeCaseSingular}.php", $this->replaceModulePlaceholders($this->getStub('lang.stub'), $moduleName, $singularName, $pluralName, $snakeCaseSingular, $camelCaseSingular));


        // 4. Call dedicated commands for other files
        // These commands handle their own stub loading and replacements,
        // making the MakeModule command cleaner.

        // Seeder (initial)
        Artisan::call('make:module-seeder', [
            'module' => $moduleName,
            'name' => "{$singularName}Seeder",
            '--model' => $singularName,
        ], $this->getOutput()); // Pass output to see command results

        $this->info("Module '{$moduleName}' created successfully!");
        $this->warn("Remember to add '{$moduleName}' to the 'enabled' array in your config/modules.php file to activate it!");

        return Command::SUCCESS;
    }

    /**
     * Check if a directory exists and is not empty.
     *
     * @param string $path
     * @return bool
     */
    protected function directoryExistsAndNotEmpty(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        $iterator = new FilesystemIterator($path);
        return $iterator->valid();
    }

    /**
     * Create a file with given content.
     *
     * @param string $path
     * @param string $content
     * @return void
     */
    protected function createFile(string $path, string $content): void
    {
        file_put_contents($path, $content);
        $this->line("Created: <info>{$path}</info>");
    }

    /**
     * Get stub contents.
     *
     * @param string $stubName
     * @return string
     */
    protected function getStub(string $stubName): string
    {
        return file_get_contents("{$this->getStubPath()}/{$stubName}");
    }

    /**
     * Replace module-specific placeholders in the stub.
     * This method is now only used for core files that don't have dedicated 'make:module-*' commands.
     *
     * @param string $stub
     * @param string $moduleName // This is the PascalCase module name (e.g., 'User')
     * @param string $singularName
     * @param string $pluralName
     * @param string $snakeCaseSingular
     * @param string $camelCaseSingular
     * @param string|null $dynamicClassName // For specific class names like StoreRequest, UpdateRequest
     * @return string
     */
    protected function replaceModulePlaceholders(
        string $stub,
        string $moduleName,
        string $singularName,
        string $pluralName,
        string $snakeCaseSingular,
        string $camelCaseSingular,
        ?string $dynamicClassName = null,
        ?string $snakeCasePlural = null,
    ): string {
        // This handles the 'DummyModule' placeholder in comments or class names
        $stub = str_replace('DummyModule', $moduleName, $stub);

        // This handles the 'DUMMY_MODULE_UPPER_SNAKE_CASE' for environment variables
        $stub = str_replace('DUMMY_MODULE_UPPER_SNAKE_CASE', Str::upper(Str::snake($moduleName)), $stub);

        // General placeholders for the main class being generated
        $stub = str_replace('DummyClass', $dynamicClassName ?? $singularName, $stub);

        // Placeholders for related model/entity names and their various cases
        $stub = str_replace('DummySingularClass', $singularName, $stub);
        $stub = str_replace('dummySingularCamelCase', $camelCaseSingular, $stub);
        $stub = str_replace('DummyPluralClass', $pluralName, $stub);
        $stub = str_replace('dummy_singular_snake_case', $snakeCaseSingular, $stub);
        $stub = str_replace('dummy_plural_snake_case', $snakeCasePlural, $stub);
        $stub = str_replace('dummy_table_name', $snakeCasePlural, $stub); // For migration stub
        $stub = str_replace('dummy_snake_case_singular', $snakeCaseSingular, $stub); // Used for config/lang filename, service provider 'nameLower'

        return $stub;
    }
}
