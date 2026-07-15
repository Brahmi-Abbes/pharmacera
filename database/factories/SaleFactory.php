<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'total'          => 0, // real total is built up as items are added
            'payment_method' => fake()->randomElement(['cash', 'card', 'insurance']),
        ];
    }
}
