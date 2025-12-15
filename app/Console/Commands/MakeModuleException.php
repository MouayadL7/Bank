<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleException extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-exception {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module exception class.';

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
        $exceptionName = Str::studly($this->argument('name')); // e.g., UserNotFoundException

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Exceptions"; // Define the target directory
        $filePath = "{$targetDirectory}/{$exceptionName}Exception.php";

        // Check if the module directory exists
        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        // Check if the exception file already exists
        if ($this->files->exists($filePath)) {
            $this->error("Exception '{$exceptionName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/exception.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $exceptionName);

        $this->files->put($filePath, $content);

        $this->info("Exception '{$exceptionName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the exception stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $exceptionName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $exceptionName): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('DummyClass', $exceptionName, $stub);

        return $stub;
    }
}
