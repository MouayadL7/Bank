<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Modules\AccessControl\Models\Role;
use Modules\User\Enums\UserStatus;

class ManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'uuid'     => Str::uuid(),
            'name'     => 'Manager',
            'email'    => 'manager@system.com',
            'password' => Hash::make('12345678'),
            'role_id'  => Role::ROLE_MANAGER,
            'status'   => UserStatus::ACTIVE->value,
        ]);
    }
}
