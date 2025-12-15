<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeModuleCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-crud {module} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all necessary files for a new module model (model, migration, controller, requests, resource, service, repository, factory).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleName = Str::studly($this->argument('module'));
        $modelName = Str::studly($this->argument('name')); // e.g., Product, User

        // Derived names
        $singularName = Str::singular($modelName); // Product, User
        $pluralName = Str::plural($modelName);     // Products, Users
        $snakeCaseSingular = Str::snake($singularName); // product, user
        $snakeCasePlural = Str::snake($pluralName);     // products, users
        $camelCaseSingular = Str::camel($singularName); // product, user

        $this->info("Generating CRUD files for model '<fg=yellow>{$modelName}</>' in module '<fg=yellow>{$moduleName}</>'...");

        // Ensure module exists (or let individual commands handle their own checks)
        $modulePath = base_path("Modules/{$moduleName}");
        if (!is_dir($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist! Please create it first using 'make:module {$moduleName}'.");
            return Command::FAILURE;
        }

        // 1. Model
        $this->line('<fg=cyan>Creating Model, Migration, and Factory...</>');
        // Pass $this->getOutput() to make sure nested command output is shown
        Artisan::call('make:module-model', [
            'module' => $moduleName,
            'name'   => $modelName,
        ], $this->getOutput());

        // 2. Migration
        $this->line('<fg=cyan>Creating Migration...</>');
        Artisan::call('make:module-migration', [
            'module'   => $moduleName,
            'name'     => "create_{$snakeCasePlural}_table", // Convention for creating tables
            '--create' => $snakeCasePlural, // Explicitly tell it to use create stub and table name
        ], $this->getOutput());

        // 3. Controller
        $this->line('<fg=cyan>Creating Controller...</>');
        Artisan::call('make:module-controller', [
            'module' => $moduleName,
            'name'   => $modelName,
        ], $this->getOutput());

        // 4. Store and Update Requests
        $this->line('<fg=cyan>Creating Store and Update Requests...</>');
        Artisan::call('make:module-request', [
            'module' => $moduleName,
            'name' => $singularName,
            '--type' => 'store',
        ], $this->getOutput());
        Artisan::call('make:module-request', [
            'module' => $moduleName,
            'name' => $singularName,
            '--type' => 'update',
        ], $this->getOutput());

        // 5. Resource
        $this->line('<fg=cyan>Creating Resource...</>');
        Artisan::call('make:module-resource', [
            'module' => $moduleName,
            'name' => $modelName, // Pass the model name, MakeModuleResource appends 'Resource'
        ], $this->getOutput());

        // 6. Service
        $this->line('<fg=cyan>Creating Service...</>');
        Artisan::call('make:module-service', [
            'module' => $moduleName,
            'name' => $singularName,
        ], $this->getOutput());

        // 7. Repository (Interface and Eloquent Implementation)
        $this->line('<fg=cyan>Creating Repository...</>');
        Artisan::call('make:module-repository', [
            'module' => $moduleName,
            'name' => $singularName, // Pass the singular model name
        ], $this->getOutput());

        // 8. DTO (Data Transfer Object)
        $this->line('<fg=cyan>Creating DTO...</>');
        Artisan::call('make:module-dto', [
            'module' => $moduleName,
            'name'   => $singularName
        ], $this->getOutput());


        $this->info("All CRUD files for '{$modelName}' in module '{$moduleName}' generated successfully!");

        return Command::SUCCESS;
    }
}
