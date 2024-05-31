<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // Create the Administrator
        User::factory()
            ->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);

        // Create users
        $nbEntries = 10_000;
        $nbEntriesPerStep = 500;
        $steps = $nbEntries / $nbEntriesPerStep;

        $this->withProgressBar($steps, function () use ($nbEntriesPerStep) {
            User::factory()
                ->count($nbEntriesPerStep)
                ->make()
                ->chunk(500)
                ->each(function ($chunk) {
                    User::insert(
                        $chunk
                            ->setVisible([])
                            ->setHidden(['profile_photo_url'])
                            ->toArray()
                    );
                });
        });
    }
}
