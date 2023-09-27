<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user' => User::factory()->create()->id,
            'name' => fake()->word(3),
            'description' => fake()->paragraph(5),
            'publish' => 1,
            'expiry' => fake()->dateTimeBetween('now', '+30 days'),
            'status' => 'active',
        ];
    }
}
