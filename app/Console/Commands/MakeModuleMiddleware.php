<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-middleware {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new middleware file for a specific module.';

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
        $middlewareName = Str::studly($this->argument('name')); // e.g., "Auth" for AuthMiddleware

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Http/Middleware"; // Define the target directory
        $filePath = "{$targetDirectory}/{$middlewareName}Middleware.php";

        if (!$this->files->isDirectory("{$modulePath}/Http/Middleware")) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        if ($this->files->exists($filePath)) {
            $this->error("Middleware file '{$filePath}' already exists!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/middleware.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $middlewareName);

        $this->files->put($filePath, $content);

        $this->info("Middleware file '{$filePath}' created successfully!");
        $this->warn("Remember to register this middleware in your module's RouteServiceProvider or a global middleware group.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the middleware stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $middlewareName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $middlewareName): string
    {
        $stub = str_replace('Modules\\DummyModule', "Modules\\{$moduleName}", $stub);
        $stub = str_replace('DummyClass', $middlewareName, $stub);

        return $stub;
    }
}
