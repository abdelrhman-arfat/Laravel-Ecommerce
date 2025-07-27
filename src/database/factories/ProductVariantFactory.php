<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color' => $this->faker->randomElement(['red', 'green', 'blue', 'black', 'white', 'brown', 'orange']),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL', 'XXXL']),
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
