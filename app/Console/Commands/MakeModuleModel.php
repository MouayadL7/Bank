<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-model {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Eloquent model.';

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
        $modelName = Str::studly($this->argument('name')); // e.g., Course
        $pluralName = Str::plural($modelName); // e.g., Courses
        $snakeCasePlural = Str::snake($pluralName); // e.g., courses

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/Models/{$modelName}.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("Model '{$modelName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/model.stub'));

        $content = str_replace(
            [
                'DummyModule',
                'DummyClass',
                'dummy_plural_snake_case',
            ],
            [
                $moduleName,
                $modelName,
                $snakeCasePlural,
            ],
            $stub
        );

        $this->files->put($filePath, $content);

        $this->info("Model '{$modelName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
