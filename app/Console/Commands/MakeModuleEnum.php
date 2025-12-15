<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-enum {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module enum class.';

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
        $enumName = Str::studly($this->argument('name')); // e.g., UserStatusEnum

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Enums"; // Define the target directory
        $filePath = "{$targetDirectory}/{$enumName}Enum.php";

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

        // Check if the enum file already exists
        if ($this->files->exists($filePath)) {
            $this->error("Enum '{$enumName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/enum.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $enumName);

        $this->files->put($filePath, $content);

        $this->info("Enum '{$enumName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the enum stub.
     *
     * @param string $stub The content of the stub file.
     * @param string $moduleName The PascalCase name of the module (e.g., 'User').
     * @param string $enumName The PascalCase name of the enum class (e.g., 'UserStatusEnum').
     * @return string The stub content with placeholders replaced.
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $enumName): string
    {
        $replacements = [
            'Modules\\DummyModule' => "Modules\\{$moduleName}", // Namespace
            'DummyClass'           => $enumName,                // Enum class name
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );
    }
}
