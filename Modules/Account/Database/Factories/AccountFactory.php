<?php

namespace Modules\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Account\Models\DummyModelClass;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Account\Models\DummyModelClass>
 */
class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = DummyModelClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }
}
