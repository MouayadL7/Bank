<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-job {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module job class.';

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
        $jobName = Str::studly($this->argument('name'));

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/Jobs/{$jobName}.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("Job '{$jobName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/job.stub'));

        $content = $this->replacePlaceholders($stub, $moduleName, $jobName);

        $this->files->put($filePath, $content);

        $this->info("Job '{$jobName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the job stub.
     *
     * @param string $stub
     * @param string $moduleName
     * @param string $jobName
     * @return string
     */
    protected function replacePlaceholders(string $stub, string $moduleName, string $jobName): string
    {
        $stub = str_replace('Modules\\DummyModule', "Modules\\{$moduleName}", $stub);
        $stub = str_replace('DummyClass', $jobName, $stub);

        return $stub;
    }
}
