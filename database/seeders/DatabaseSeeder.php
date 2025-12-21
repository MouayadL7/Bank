<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\AccessControl\Database\Seeders\RoleSeeder;
use Modules\User\Database\Seeders\AdminSeeder;
use Modules\User\Database\Seeders\ManagerSeeder;
use Modules\User\Database\Seeders\TellerSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            ManagerSeeder::class,
            TellerSeeder::class,
        ]);
    }
}
