<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-request {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new module request file';

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
        $requestName = Str::studly($this->argument('name')); // e.g., CreateUserRequest, UserLoginRequest

        $modulePath = base_path("Modules/{$moduleName}");
        $requestPath = "{$modulePath}/Http/Requests";
        $filePath = "{$requestPath}/{$requestName}.php";

        // Check if the module directory exists
        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the Requests directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($requestPath)) {
            $this->files->makeDirectory($requestPath, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$requestPath}</info>");
        }

        // Check if the request file already exists
        if ($this->files->exists($filePath)) {
            $this->error("Request '{$requestName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path("Console/stubs/module/request.stub"));

        $content = $this->replacePlaceholders($stub, $moduleName, $requestName);

        $this->files->put($filePath, $content);

        $this->info("Request '{$requestName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the request stub.
     *
     * @param string $stub The content of the stub file.
     * @param string $moduleName The PascalCase name of the module (e.g., 'User').
     * @param string $requestName The PascalCase name of the request class (e.g., 'StoreUserRequest').
     * @param string|null $modelName The PascalCase name of the model (e.g., 'User'), if applicable.
     * @return string The stub content with placeholders replaced.
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $requestName): string
    {
        $replacements = [
            'Modules\\DummyModule' => "Modules\\{$moduleName}", // Namespace
            'DummyClass'           => $requestName,             // Request class name
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
