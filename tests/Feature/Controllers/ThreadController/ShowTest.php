<?php

use App\Http\Resources\PostResource;
use App\Http\Resources\ThreadResource;
use App\Models\Post;
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

it('passes posts to the view', function () {
    $thread = Thread::factory()->create();
    $posts = Post::factory(10)->for($thread)->create();

    $posts->load('user');

    get(route('threads.show', $thread))
        ->assertHasPaginatedResource(
            'posts',
            PostResource::collection(
                $posts->sortBy('created_at')
            )
        );
});
