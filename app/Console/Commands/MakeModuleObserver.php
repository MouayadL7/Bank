<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-scope {module} {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module observer class.';

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
        $observerName = Str::studly($this->argument('name')); // e.g., Active
        $modelName = Str::studly($this->option('model') ?? Str::singular(str_replace('Factory', '', $observerName))); // e.g., Course

        $camelCaseSingular = Str::camel($modelName); // e.g., course, blogPost

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Observers"; // Define the target directory
        $filePath = "{$targetDirectory}/{$observerName}Observer.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        if ($this->files->exists($filePath)) {
            $this->error("Observer '{$observerName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/observer.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $observerName, $modelName, $camelCaseSingular);

        $this->files->put($filePath, $content);

        $this->info("Observer '{$observerName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the observer stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $observerName
     * @param string $modelName
     * @param string $camelCaseSingular
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $scopeName, string $modelName, string $camelCaseSingular): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('DummyClass', $scopeName, $stub);
        $stub = str_replace('DummyModelClass', $modelName, $stub);
        $stub = str_replace('dummy_camel_case_singular', $camelCaseSingular, $stub);

        return $stub;
    }
}
