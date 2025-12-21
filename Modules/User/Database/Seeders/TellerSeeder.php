<?php

namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Illuminate\Support\Str;
use Modules\AccessControl\Models\Role;
use Modules\User\Enums\UserStatus;

class TellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'uuid'     => Str::uuid(),
            'name'     => 'Teller',
            'email'    => 'teller@system.com',
            'password' => Hash::make('12345678'),
            'role_id'  => Role::ROLE_TELLER,
            'status'   => UserStatus::ACTIVE->value,
        ]);
    }
}
