<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

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
            'body' => Collection::times(4, fn () => fake()->realTextBetween(100, 600))->join(PHP_EOL . PHP_EOL),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    // TODO: a post can be edited, and all versions will be kept.

    /**
     * Indicate that a thread should be created with the post
     */
    public function withThread(): Factory
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create();

            return [
                'user_id' => $user->id,
                'thread_id' => Thread::factory()->create([
                    'user_id' => $user->id,
                    'created_at' => $attributes['created_at'],
                    'updated_at' => $attributes['updated_at'],
                ]),
            ];
        })->afterCreating(function (Post $post) {
            $post->thread->first_post_id = $post->id;
            $post->save();
        });
    }

    public function recentlyPosted(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
    }
}
