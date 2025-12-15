<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-repository {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new module repository interface and Eloquent implementation.';

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
        $repositoryName = Str::studly($this->argument('name')); // e.g., Course
        $singularName = $repositoryName; // The repository name is usually the singular model name
        $camelCaseSingular = Str::camel($singularName);

        $modulePath = base_path("Modules/{$moduleName}");
        $interfacePath = "{$modulePath}/Repositories/Interfaces";
        $eloquentPath = "{$modulePath}/Repositories/Eloquent";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Create Interface
        $interfaceFileName = "{$singularName}RepositoryInterface";
        $interfaceFilePath = "{$interfacePath}/{$interfaceFileName}.php";
        if ($this->files->exists($interfaceFilePath)) {
            $this->warn("Repository Interface '{$interfaceFileName}' already exists in module '{$moduleName}'. Skipping.");
        } else {
            $stub = $this->files->get(app_path('Console/stubs/module/repository-interface.stub'));
            $content = str_replace(
                [
                    'DummyModule',
                    'DummyClass',
                    'DummySingularClass',
                    'dummySingularCamelCase',
                ],
                [
                    $moduleName,
                    $singularName,
                    $singularName,
                    $camelCaseSingular,
                ],
                $stub
            );
            $this->files->put($interfaceFilePath, $content);
            $this->info("Repository Interface '{$interfaceFileName}' created successfully in module '{$moduleName}'.");
        }

        // Create Eloquent Implementation
        $eloquentFileName = "{$singularName}Repository";
        $eloquentFilePath = "{$eloquentPath}/{$eloquentFileName}.php";
        if ($this->files->exists($eloquentFilePath)) {
            $this->warn("Eloquent Repository '{$eloquentFileName}' already exists in module '{$moduleName}'. Skipping.");
        } else {
            $stub = $this->files->get(app_path('Console/stubs/module/repository-eloquent.stub'));
            $content = str_replace(
                [
                    'DummyModule',
                    'DummyClass',
                    'DummySingularClass',
                    'dummySingularCamelCase',
                ],
                [
                    $moduleName,
                    $singularName,
                    $singularName,
                    $camelCaseSingular,
                ],
                $stub
            );
            $this->files->put($eloquentFilePath, $content);
            $this->info("Eloquent Repository '{$eloquentFileName}' created successfully in module '{$moduleName}'.");
        }

        return Command::SUCCESS;
    }
}
