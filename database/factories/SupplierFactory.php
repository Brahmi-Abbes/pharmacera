<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'name'   => fake()->company(),
            'phone'  => fake()->numerify('05########'),
            'email'  => fake()->unique()->safeEmail(),
            'wilaya' => fake()->randomElement(['Alger', 'Oran', 'Constantine', 'Sétif', 'Annaba']),
        ];
    }
}
