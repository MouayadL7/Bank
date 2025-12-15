<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-helper {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new helper file for a specific module.';

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
        $helperName = Str::studly($this->argument('name')); // e.g., "User" for UserHelpers
        $snakeCaseHelperName = Str::snake($helperName);
        $camelCaseHelperName = Str::camel($helperName);

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Helpers"; // Define the target directory
        $filePath = "{$targetDirectory}/{$helperName}Helper.php";

        if (!$this->files->isDirectory("{$modulePath}/Helpers")) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        if ($this->files->exists($filePath)) {
            $this->error("Helper file '{$filePath}' already exists!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/helper.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $snakeCaseHelperName, $camelCaseHelperName);

        $this->files->put($filePath, $content);

        $this->info("Helper file '{$filePath}' created successfully!");
        $this->warn("Remember to include this helper file in your module's ServiceProvider or composer.json for auto-loading.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the helper stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $snakeCaseHelperName
     * @param string $camelCaseHelperName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $snakeCaseHelperName, string $camelCaseHelperName): string
    {
        $stub = str_replace('DummyModule', $moduleName, $stub);
        $stub = str_replace('dummy_singular_snake_case', $snakeCaseHelperName, $stub);
        $stub = str_replace('dummySingularCamelCase', $camelCaseHelperName, $stub);

        return $stub;
    }
}
