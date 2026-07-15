<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition(): array
    {
        return [
            'category_id'     => Category::factory(),
            'name'             => fake()->unique()->word() . ' ' . fake()->randomElement(['500mg', '1000mg', 'Forte']),
            'generic_name'     => fake()->word(),
            'unit'             => fake()->randomElement(['box', 'bottle', 'tube']),
            'barcode'          => fake()->unique()->ean13(),
            'selling_price'    => fake()->randomFloat(2, 100, 2000),
            'purchase_price'   => fake()->randomFloat(2, 50, 1500),
            'alert_threshold'  => fake()->numberBetween(5, 20),
        ];
    }
}
