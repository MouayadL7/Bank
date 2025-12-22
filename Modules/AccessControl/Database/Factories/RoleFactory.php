<?php

namespace Modules\AccessControl\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\AccessControl\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\AccessControl\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'manager', 'teller', 'customer']),
        ];
    }
}

