<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-listener {module} {name} {--event=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module listener class.';

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
        $listenerName = Str::studly($this->argument('name')); // e.g., HandleUserCreated
        $eventName = Str::studly($this->option('event') ?? Str::singular(str_replace('Handle', '', $listenerName))); // e.g., UserCreated

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Listeners"; // Define the target directory
        $filePath = "{$targetDirectory}/{$listenerName}Listener.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        $stub = $this->files->get(app_path('Console/stubs/module/listener.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $listenerName, $eventName);

        $this->files->put($filePath, $content);

        $this->info("Listener '{$listenerName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the listener stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $listenerName
     * @param string $eventName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $listenerName, string $eventName): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('DummyClass', $listenerName, $stub);
        $stub = str_replace('DummyEventClass', $eventName, $stub);

        return $stub;
    }
}
