<?php

namespace Database\Seeders;

use Closure;
use Illuminate\Database\Seeder;
use Symfony\Component\Console\Helper\ProgressBar;

class BaseSeeder extends Seeder
{
    protected function withProgressBar(int $steps, Closure $createCollectionOfOne, int $amountPerStep = 1): void
    {
        $progressBar = new ProgressBar($this->command->getOutput(), $steps * $amountPerStep);

        $progressBar->start();

        foreach (range(1, $steps) as $i) {
            $createCollectionOfOne();
            $progressBar->advance($amountPerStep);
        }

        $progressBar->finish();

        $this->command->getOutput()->writeln('');
    }
}
