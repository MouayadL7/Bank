<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-action {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module action class.';

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
        $actionName = Str::studly($this->argument('name')); // e.g., UserStatusAction

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Actions"; // Define the target directory
        $filePath = "{$targetDirectory}/{$actionName}Action.php";

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

        // Check if the action file already exists
        if ($this->files->exists($filePath)) {
            $this->error("Action '{$actionName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/action.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $actionName);

        $this->files->put($filePath, $content);

        $this->info("Action '{$actionName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the action stub.
     *
     * @param string $stub The content of the stub file.
     * @param string $moduleName The PascalCase name of the module (e.g., 'User').
     * @param string $actionName The PascalCase name of the action class (e.g., 'UserStatusAction').
     * @return string The stub content with placeholders replaced.
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $actionName): string
    {
        $replacements = [
            'DummyModule' => $moduleName,      // Namespace
            'DummyClass'  => $actionName,      // Action class name
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
