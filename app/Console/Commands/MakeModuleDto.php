<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleDTO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-dto {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module Data Transfer Object (DTO).';

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
        $dtoName = Str::studly($this->argument('name')); // e.g., CourseData, UserProfileData

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/DTOs/{$dtoName}Data.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("DTO '{$dtoName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/dto.stub'));

        $content = str_replace(
            [
                'DummyModule',
                'DummyClass',
            ],
            [
                $moduleName,
                $dtoName,
            ],
            $stub
        );

        $this->files->put($filePath, $content);

        $this->info("DTO '{$dtoName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
