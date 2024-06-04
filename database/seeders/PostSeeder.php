<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PostSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Post::truncate();
        Schema::enableForeignKeyConstraints();

        // we assume threads ID go from 1 to $nbThreads, and that all entries exist
        $nbThreads = Thread::count();

        // we assume users ID go from 1 to $nbUsers, and that all entries exist
        $nbUsers = User::count();

        $threadId = 1;

        // For each thread, create posts
        $this->withProgressBar($nbThreads, function () use (&$threadId, $nbUsers) {
            Log::info('Thread ' . $threadId);
            // For this thread, we create a specific number of posts
            $nbPostsPerThreadMin = 10;
            $nbPostsPerThreadMax = 50;
            $nbPostsCurrentThread = rand($nbPostsPerThreadMin, $nbPostsPerThreadMax);

            // Chunk size to be inserted in the database
            // We insert 500 posts per query, unless the number of posts to create is smaller
            $chunkSize = min($nbPostsCurrentThread, 250);

            // If the number of posts to create is more than 500, we need to do that in chunks,
            // otherwise we will run OOM
            $nbPostsToCreatePerChunkMax = 250;

            for ($i = 1; $i <= ceil($nbPostsCurrentThread / $nbPostsToCreatePerChunkMax); $i++) {
                if ($i <= $nbPostsCurrentThread / $nbPostsToCreatePerChunkMax) {
                    $nbPostsToCreate = $nbPostsToCreatePerChunkMax;
                } else {
                    $nbPostsToCreate = $nbPostsCurrentThread % $nbPostsToCreatePerChunkMax;
                }

                Post::factory()
                    ->count($nbPostsToCreate)
                    ->state(new Sequence(
                        fn (Sequence $sequence) => [
                            'user_id' => rand(1, $nbUsers),
                            'thread_id' => $threadId,
                        ],
                    ))
                    ->make()
                    ->chunk($chunkSize)
                    ->each(function ($chunk) {
                        Post::insert($chunk->toArray());
                    });
            }

            $threadId++;
        });
    }
}
