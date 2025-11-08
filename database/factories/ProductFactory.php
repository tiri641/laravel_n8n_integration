<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->realText(20),
            'description' => fake()->realText(200), 
            'price'       => fake()->numberBetween(100, 100000),
            'stock'       => fake()->numberBetween(0, 100),
            'is_active' => fake()->boolean(80), // 80%の確率でtrue
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at'  => function(array $attributes) {
                return fake()->dateTimeBetween($attributes['created_at'], 'now');
            }
        ];
    }
}
