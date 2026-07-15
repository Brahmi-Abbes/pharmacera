<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(20, 200);

        return [
            'medicine_id'        => Medicine::factory(),
            'supplier_id'        => Supplier::factory(),
            'quantity'           => $quantity,
            'remaining_quantity' => $quantity,
            'purchase_price'     => fake()->randomFloat(2, 50, 1500),
            'expiry_date'        => fake()->dateTimeBetween('+30 days', '+2 years'),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'expiry_date' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    public function empty(): static
    {
        return $this->state(fn () => [
            'remaining_quantity' => 0,
        ]);
    }
}
