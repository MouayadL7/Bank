<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-trait {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module trait (trait) file.';

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
        $traitName = Str::studly($this->argument('name')); // e.g., HasUserTrait

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Traits"; // Define the target directory
        $filePath = "{$targetDirectory}/{$traitName}.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        $stub = $this->files->get(app_path('Console/stubs/module/trait.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $traitName);

        $this->files->put($filePath, $content);

        $this->info("Trait '{$traitName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the trait stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $traitName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $traitName): string
    {
        $stub = str_replace('Modules\\DummyModule', "Modules\\{$moduleName}", $stub);
        $stub = str_replace('DummyClass', $traitName, $stub);

        return $stub;
    }
}
