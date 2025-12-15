<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-event {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module event class.';

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
        $eventName = Str::studly($this->argument('name')); // e.g., UserCreated

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Events"; // Define the target directory
        $filePath = "{$targetDirectory}/{$eventName}Event.php";

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
            $this->error("Event '{$eventName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/event.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $eventName);

        $this->files->put($filePath, $content);

        $this->info("Event '{$eventName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the event stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $eventName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $eventName): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('DummyClass', $eventName, $stub);

        return $stub;
    }
}
