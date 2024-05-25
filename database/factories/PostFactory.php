<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-5 years', 'now');
        $updatedAt = fake()->dateTimeBetween($createdAt, 'now');

        return [
            'user_id' => User::factory(),
            'thread_id' => Thread::factory(),
            'parent_id' => null,
            'body' => fake()->realTextBetween(100, 600),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    // TODO: a post can be edited, and all versions will be kept.
}
