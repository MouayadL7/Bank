<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-resource {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module API resource.';

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
        $resourceName = Str::studly($this->argument('name')); // e.g., CourseResource
        $singularName = Str::singular(str_replace('Resource', '', $resourceName)); // e.g., Course

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/Http/Resources/{$resourceName}Resource.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("Resource '{$resourceName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/resource.stub'));

        $content = str_replace(
            [
                'DummyModule',
                'DummyClass',
            ],
            [
                $moduleName,
                $resourceName,
            ],
            $stub
        );

        $this->files->put($filePath, $content);

        $this->info("Resource '{$resourceName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
