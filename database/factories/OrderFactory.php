<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
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
            'customer_name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'status' => fake()->randomElement(['pending', 'paid', 'cancelled', 'refunded']),
        ];
    }

    /**
     * Orders placed today.
     */
    public function today(): static
    {
        return $this->state(function (): array {
            $orderedAt = fake()->dateTimeBetween('today', 'now');

            return [
                'created_at' => $orderedAt,
                'updated_at' => $orderedAt,
            ];
        });
    }

    /**
     * Orders placed in the past (excluding today).
     */
    public function past(): static
    {
        return $this->state(function (): array {
            $orderedAt = fake()->dateTimeBetween('-90 days', 'yesterday');

            return [
                'created_at' => $orderedAt,
                'updated_at' => $orderedAt,
            ];
        });
    }
}
