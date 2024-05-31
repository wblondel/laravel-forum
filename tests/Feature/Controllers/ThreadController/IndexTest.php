<?php

use App\Http\Resources\ThreadResource;
use App\Models\Thread;

use function Pest\Laravel\get;

it('should return the correct component', function () {
    get(route('threads.index'))
        ->assertComponent('Threads/Index');
});

it('passes threads to the view', function () {
    $threads = Thread::factory(5)->hasPosts(10)->create();

    $paginatedResource = ThreadResource::collection($threads
        ->load([
            'firstPost.user:id,name,profile_photo_path',
            'latestPost.user:id,name,profile_photo_path',
        ])
        ->sortByDesc(function ($thread) {
            return $thread->latestPost->created_at;
        })
        ->loadCount('posts')
    );

    get(route('threads.index'))
        ->assertHasPaginatedResource('threads', $paginatedResource);
});
