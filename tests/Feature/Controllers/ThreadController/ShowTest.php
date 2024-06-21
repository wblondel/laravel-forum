<?php

use App\Http\Resources\PostResource;
use App\Http\Resources\ThreadResource;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\get;

it('can show a thread', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $firstPost = $posts['first_post'];

    get($firstPost->thread->showRoute())
        ->assertComponent('Threads/Show');
});

it('passes a thread with user data to the view', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $firstPost = $posts['first_post'];
    $recentPost = $posts['recent_post'];

    $firstPost->thread->load('user');

    get($firstPost->thread->showRoute())
        ->assertHasResource('thread', ThreadResource::make($firstPost->thread));
});

it('passes posts to the view', function () {
    $thread = Post::factory()->withThread()->create()->thread;

    Post::factory(100)
        ->for($thread)
        ->for(User::factory())
        ->recentlyPosted()
        ->create();

    $thread->refresh()->load('posts.user');

    get($thread->showRoute())
        ->assertHasPaginatedResource(
            'posts',
            PostResource::collection($thread->posts
                ->sortBy('created_at')
                ->take(config('pagination.posts_per_page_on_thread'))
            )
        );
});
