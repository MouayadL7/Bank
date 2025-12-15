<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-controller {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module controller.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleName = Str::studly($this->argument('module'));
        $controllerName = Str::studly($this->argument('name')); // e.g., CourseController

        // Infer singular/plural names from the controller name
        $baseName = str_replace('Controller', '', $controllerName);
        $singularName = Str::singular($baseName); // e.g., Course
        $pluralName = Str::plural($singularName); // e.g., Courses
        $snakeCaseSingular = Str::snake($singularName); // e.g., course
        $snakeCasePlural = Str::snake($pluralName); // e.g., courses
        $camelCaseSingular = Str::camel($singularName); // e.g., course

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Http/Controllers"; // Define the target directory
        $filePath = "{$targetDirectory}/{$controllerName}Controller.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        // Check if the controller file already exists
        if ($this->files->exists($filePath)) {
            $this->error("Controller '{$controllerName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        // Get the stub content
        $stub = $this->files->get(app_path('Console/stubs/module/controller.stub'));

        // Replace placeholders in the stub
        $content = $this->replacePlaceholders(
            $stub,
            $moduleName,
            $controllerName,
            $singularName,
            $pluralName,
            $snakeCaseSingular,
            $snakeCasePlural,
            $camelCaseSingular
        );

        // Write the content to the new file
        $this->files->put($filePath, $content);

        $this->info("Controller '{$controllerName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the controller stub.
     *
     * @param string $stub The content of the stub file.
     * @param string $moduleName The PascalCase name of the module (e.g., 'User').
     * @param string $controllerName The PascalCase name of the controller (e.g., 'UserController').
     * @param string $singularName The PascalCase singular name (e.g., 'User').
     * @param string $pluralName The PascalCase plural name (e.g., 'Users').
     * @param string $snakeCaseSingular The snake_case singular name (e.g., 'user').
     * @param string $snakeCasePlural The snake_case plural name (e.g., 'users').
     * @param string $camelCaseSingular The camelCase singular name (e.g., 'user').
     * @return string The stub content with placeholders replaced.
     */
    protected function replacePlaceholders(
        string $stub,
        string $moduleName,
        string $controllerName,
        string $singularName,
        string $pluralName,
        string $snakeCaseSingular,
        string $snakeCasePlural,
        string $camelCaseSingular
    ): string {
        $replacements = [
            'Modules\\DummyModule'        => "Modules\\{$moduleName}",   // Namespace
            'DummyModule'                 => $controllerName,            // Controller class name
            'DummySingularClass'          => $singularName,              // PascalCase singular
            'DummyPluralClass'            => $pluralName,                // PascalCase plural
            'dummy_singular_snake_case'   => $snakeCaseSingular,         // snake_case singular (for views)
            'dummy_plural_snake_case'     => $snakeCasePlural,           // snake_case plural
            'dummySingularCamelCase'      => $camelCaseSingular,         // camelCase singular
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
