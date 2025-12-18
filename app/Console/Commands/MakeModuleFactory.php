<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-factory {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module factory.';

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
        $factoryName = Str::studly($this->argument('name')); // e.g., CourseFactory

        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Factories";
        $filePath = "{$targetDirectory}/{$factoryName}Factory.php";

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
            $this->error("Factory '{$factoryName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/factory.stub'));

        $content = str_replace(
            [
                'DummyModule',
                'DummyClass',
            ],
            [
                $moduleName,
                $factoryName,
            ],
            $stub
        );

        $this->files->put($filePath, $content);

        $this->info("Factory '{$factoryName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
