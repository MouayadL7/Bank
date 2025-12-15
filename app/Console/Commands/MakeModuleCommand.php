<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-command {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Artisan command.';

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
        $commandName = Str::studly($this->argument('name')); // e.g., UserCommand

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Commands"; // Define the target directory
        $filePath = "{$targetDirectory}/{$commandName}Command.php";

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
            $this->error("Command '{$commandName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/command.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $commandName);

        $this->files->put($filePath, $content);

        $this->info("Command '{$commandName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the command stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $commandName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $commandName): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('DummyClass', $commandName, $stub);
        $stub = str_replace('dummy_command_signature', Str::snake(str_replace('Command', '', $commandName)) . ':do-something', $stub);

        return $stub;
    }
}
