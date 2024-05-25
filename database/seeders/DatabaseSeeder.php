<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();

        $threads = Thread::factory(100)
            ->recycle($users)
            ->has(Post::factory(20)->recycle($users))
            ->create();

        User::factory()
            ->has(Post::factory(45)->recycle($threads))
            ->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
    }
}
