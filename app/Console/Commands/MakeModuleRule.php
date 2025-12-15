<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleRule extends Command
{
    protected $signature = 'make:module-rule {module} {name}';
    protected $description = 'Create a new Rule class in the specified module.';
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $ruleName = Str::studly($this->argument('name')); // e.g., UniqueEmailRule

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Rules";
        $filePath = "{$targetDirectory}/{$ruleName}Rule.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true);
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        if ($this->files->exists($filePath)) {
            $this->error("Rule '{$ruleName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/rule.stub'));
        $content = $this->replacePlaceholders($stub, $moduleName, $ruleName);

        $this->files->put($filePath, $content);

        $this->info("Rule '{$ruleName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    protected function replacePlaceholders(string $stub, string $moduleName, string $ruleName): string
    {
        return str_replace(
            ['Modules\\DummyModule', 'DummyClass'],
            ["Modules\\{$moduleName}", $ruleName],
            $stub
        );
    }
}
