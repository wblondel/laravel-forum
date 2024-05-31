<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Thread>
 */
class ThreadFactory extends Factory
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
            'title' => Str::beforeLast(fake()->sentence, '.'),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }
}
