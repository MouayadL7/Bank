<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-migration {module} {name} {--create=} {--table=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module migration file.';

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
        $name = $this->argument('name'); // The migration name provided by the user (e.g., create_users_table, add_email_to_users_table)

        // Determine the migration class name (e.g., CreateUsersTable, AddEmailToUsersTable)
        $className = Str::studly($name);

        // Determine the table name and stub type
        $tableName = $this->option('table');
        $create = $this->option('create'); // --create option directly specifies table name for 'create' stub

        $stubType = 'update'; // Default to update stub
        $inferredTableName = null;

        // Logic to infer table name and stub type based on migration name conventions
        if (Str::startsWith($name, 'create_') && Str::endsWith($name, '_table')) {
            $inferredTableName = Str::after(Str::before($name, '_table'), 'create_');
            $stubType = 'create';
        } elseif (Str::startsWith($name, 'add_') && Str::contains($name, '_to_') && Str::endsWith($name, '_table')) {
            $parts = explode('_to_', $name);
            $inferredTableName = Str::before($parts[1], '_table');
            $stubType = 'update';
        } elseif (Str::startsWith($name, 'remove_') && Str::contains($name, '_from_') && Str::endsWith($name, '_table')) {
            $parts = explode('_from_', $name);
            $inferredTableName = Str::before($parts[1], '_table');
            $stubType = 'update';
        }

        // Prioritize explicit --table or --create option
        if ($create) {
            $tableName = $create; // If --create is used, that's the table name
            $stubType = 'create';
        } elseif ($tableName) {
            // If --table is used, and it's not a 'create' migration, assume update
            $stubType = 'update';
        } else {
            // Fallback to inferred table name if no option is given
            $tableName = $inferredTableName;
        }

        // If table name is still not determined, it's an error for create migrations
        if ($stubType === 'create' && empty($tableName)) {
            $this->error("Could not determine table name for 'create' migration. Please use --create=table_name or follow 'create_table_name_table' convention.");
            return Command::FAILURE;
        } elseif (empty($tableName) && $stubType === 'update') {
            $this->error("Could not determine table name for 'update' migration. Please use --table=table_name or follow 'add_column_to_table_name_table' convention.");
            return Command::FAILURE;
        }


        $modulePath = base_path("Modules/{$moduleName}");
        $targetDirectory = "{$modulePath}/Database/Migrations"; // Define the target directory
        $fileName = date('Y_m_d_His') . "_{$name}.php"; // Use the raw name argument for the filename
        $filePath = "{$targetDirectory}/{$fileName}";

        // Check if the module directory exists
        if (!$this->files->isDirectory($modulePath)) {
            $this->error("Module '{$moduleName}' does not exist!");
            return Command::FAILURE;
        }

        // Ensure the target directory exists. Create it if it doesn't.
        if (!$this->files->isDirectory($targetDirectory)) {
            $this->files->makeDirectory($targetDirectory, 0755, true, true); // Recursive, force
            $this->line("Created directory: <info>{$targetDirectory}</info>");
        }

        // Check if a migration with the same name (excluding timestamp) already exists
        // This is a more robust check than just the full filename
        $existingMigrations = $this->files->glob("{$targetDirectory}/*_{$name}.php");
        if (!empty($existingMigrations)) {
            $this->error("Migration '{$name}' already exists in module '{$moduleName}'!");
            return Command::FAILURE;
        }

        // Get the correct stub based on type
        $stubPath = app_path("Console/stubs/module/migration-{$stubType}.stub");
        if (!$this->files->exists($stubPath)) {
            $this->error("Migration stub '{$stubType}.stub' not found at '{$stubPath}'!");
            return Command::FAILURE;
        }
        $stub = $this->files->get($stubPath);

        // Replace placeholders in the stub
        $content = $this->replacePlaceholders($stub, $className, $tableName);

        // Write the content to the new file
        $this->files->put($filePath, $content);

        $this->info("Migration '{$fileName}' created successfully in module '{$moduleName}'.");

        return Command::SUCCESS;
    }

    /**
     * Replace placeholders in the migration stub.
     *
     * @param string $stub The content of the stub file.
     * @param string $className The PascalCase name of the migration class.
     * @param string $tableName The snake_case name of the table.
     * @return string The stub content with placeholders replaced.
     */
    protected function replacePlaceholders(string $stub, string $className, string $tableName): string
    {
        $stub = str_replace('dummy_table_name', $tableName, $stub);
        $stub = str_replace('DummyClass', $className, $stub); // Replace the class name placeholder

        return $stub;
    }
}
