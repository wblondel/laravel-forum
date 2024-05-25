<?php

use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;

it('should return the correct component', function () {
    get(route('threads.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->component('Threads/Index', true)
        );
});

it('passes threads to the view', function () {
    get(route('threads.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->has('threads')
        );
});
