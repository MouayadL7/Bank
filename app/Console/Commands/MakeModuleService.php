<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
        protected $signature = 'make:module-service {module} {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module service class.';

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
        $serviceName = Str::studly($this->argument('name')); // e.g., CourseService
        $singularName = Str::studly($this->option('model') ?? Str::singular(str_replace('Service', '', $serviceName))); // e.g., Course
        $camelCaseSingular = Str::camel($singularName);

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/Services/{$serviceName}Service.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("Service '{$serviceName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/service.stub'));

        $content = str_replace(
            [
                'DummyModule',
                'DummyClass',
                'DummySingularClass',
                'dummySingularCamelCase',
            ],
            [
                $moduleName,
                $serviceName,
                $singularName,
                $camelCaseSingular,
            ],
            $stub
        );

        $this->files->put($filePath, $content);

        $this->info("Service '{$serviceName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
