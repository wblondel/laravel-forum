<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Sequence;

class ThreadSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // The number of threads cannot be higher than the number of posts
        $nbPosts = Post::count();

        $nbEntries = min($nbPosts, 5_000);
        $nbEntriesPerStep = 500;
        $steps = $nbEntries / $nbEntriesPerStep;

        // Get the oldest posts
        $oldestPosts = Post::oldest()->oldest('id')->get();

        $this->withProgressBar($steps, function () use ($nbEntriesPerStep, $oldestPosts) {
            Thread::factory()
                ->count($nbEntriesPerStep)
                ->state(new Sequence(
                    fn (Sequence $sequence) => [
                        'user_id' => ($oldestPost = $oldestPosts->shift())->user_id,
                        'first_post_id' => $oldestPost->id,
                        'created_at' => $oldestPost->created_at,
                    ],
                ))
                ->make()
                ->chunk(500)
                ->each(function ($chunk) {
                    Thread::insert($chunk->toArray());
                });
        });

        // Update thread ID of all first post
        $threads = Thread::with('firstPost')->get();

        foreach ($threads as $thread) {
            $thread->firstPost->thread_id = $thread->id;
            $thread->firstPost->save();
        }

        $threadsId = Thread::pluck('id')->toArray();

        // attach remaining posts to random threads
        foreach ($oldestPosts as $oldestPost) {
            $oldestPost->thread_id = $threadsId[array_rand($threadsId)];
            $oldestPost->save();
        }
    }
}
