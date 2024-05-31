<?php

namespace Database\Seeders;

use App\Models\Thread;
use Illuminate\Support\Facades\Schema;

class ThreadSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Thread::truncate();
        Schema::enableForeignKeyConstraints();

        $nbEntries = 5_000;
        $nbEntriesPerStep = 500;
        $steps = $nbEntries / $nbEntriesPerStep;

        $this->withProgressBar($steps, function () use ($nbEntriesPerStep) {
            Thread::factory()
                ->count($nbEntriesPerStep)
                ->make()
                ->chunk(500)
                ->each(function ($chunk) {
                    Thread::insert($chunk->toArray());
                });
        });
    }
}
