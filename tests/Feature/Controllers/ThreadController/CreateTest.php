<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication', function () {
    get(route('threads.create'))->assertRedirectToRoute('login');
});

it('returns the correct component', function () {
    actingAs(User::factory()->create())
        ->get(route('threads.create'))
        ->assertComponent('Threads/Create');
});
