<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'user_id'    => User::factory(),
            'total_price' => $this->faker->randomFloat(2, 50, 500), // سعر بين 50 و 500
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
