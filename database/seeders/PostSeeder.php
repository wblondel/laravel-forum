<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

class PostSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // we assume users ID go from 1 to $nbUsers, and that all entries exist
        $nbUsers = User::count();

        // Create posts
        $nbEntries = 10_000;
        $nbEntriesPerStep = 500;
        $steps = $nbEntries / $nbEntriesPerStep;

        $this->withProgressBar($steps, function () use ($nbUsers, $nbEntriesPerStep) {
            Post::factory()
                ->count($nbEntriesPerStep)
                ->state(new Sequence(
                    fn (Sequence $sequence) => [
                        'user_id' => rand(1, $nbUsers),
                    ],
                ))
                ->make([
                    'thread_id' => null,
                ])
                ->chunk(500)
                ->each(function ($chunk) {
                    Post::insert($chunk->toArray());
                });
        });
    }
}
