<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-seeder {module} {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module seeder file.';

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
        $seederName = Str::studly($this->argument('name')); // e.g., CourseSeeder
        // Determine the model name, defaulting to singular form of the seeder name (without 'Seeder')
        $modelName = Str::studly($this->option('model') ?? Str::singular(str_replace('Seeder', '', $seederName)));

        $modulePath = base_path("Modules/{$moduleName}");
        $filePath = "{$modulePath}/Database/Seeders/{$seederName}Seeder.php";

        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        if ($this->files->exists($filePath)) {
            $this->error("Seeder '{$seederName}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        $stub = $this->files->get(app_path('Console/stubs/module/seeder.stub'));

        // Define the replacements
        $replacements = [
            'DummyModule'       => $moduleName, // For namespace and model import path
            'DummyClass'        => $seederName, // For the seeder class name
            'DummyModelClass'   => $modelName,  // For the model class name (all occurrences)
        ];

        // Perform the replacements
        $content = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $stub
        );

        // Write the content to the new file
        $this->files->put($filePath, $content);

        $this->info("Seeder '{$seederName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }
}
