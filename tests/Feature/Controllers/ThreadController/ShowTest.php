<?php

use App\Http\Resources\ThreadResource;
use App\Models\Thread;

use function Pest\Laravel\get;

it('can show a thread', function () {
    $thread = Thread::factory()->hasPosts(10)->create();

    get(route('threads.show', $thread))
        ->assertComponent('Threads/Show');
});

it('passes a thread to the view', function () {
    $thread = Thread::factory()->hasPosts(10)->create();

    $thread->load('firstPost.user');

    get(route('threads.show', $thread))
        ->assertHasResource('thread', ThreadResource::make($thread));
});
