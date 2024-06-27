<?php

use App\Http\Resources\ThreadResource;
use App\Models\Thread;

use function Pest\Laravel\get;

it('should return the correct component', function () {
    get(route('threads.index'))
        ->assertComponent('Threads/Index');
});

it('passes threads to the view', function () {
    for ($i = 0; $i < 50; $i++) {
        createThreadWithPostAndAdditionalRecentPost();
    }

    $threads = Thread::all();

    $paginatedResource = ThreadResource::collection($threads
        ->load([
            'user:id,name,profile_photo_path',
            'latestPost.user:id,name,profile_photo_path',
        ])
        ->sortByDesc(function ($thread) {
            return $thread->latestPost->created_at;
        })
        ->loadCount('posts')
        ->take(config()->integer('pagination.threads_per_page'))
    );

    get(route('threads.index'))
        ->assertHasPaginatedResource('threads', $paginatedResource);
});
