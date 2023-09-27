<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bid>
 */
class BidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bidder' => User::factory()->create()->id,
            'item' => Item::factory()->create()->item,
            'amount' => fake()->numberBetween(100, 5000),
            'status' => fake()->randomElement([
                'placed', 'accepted', 'rejected', 'expired', 'withdrawn',
                'pending', 'canceled', 'completed', 'outbid', 'invalid'
            ]),
        ];
    }
}
