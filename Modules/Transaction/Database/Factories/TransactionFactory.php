<?php

namespace Modules\Transaction\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transaction\Models\DummyModelClass;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Transaction\Models\DummyModelClass>
 */
class TransactionFactory extends Factory
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
