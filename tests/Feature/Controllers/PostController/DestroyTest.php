<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {
    $post = Post::factory()->create();

    delete(route('posts.destroy', $post))
        ->assertRedirectToRoute('login');
});

it('can delete a recent post', function () {
    $thread = Thread::factory()->create();

    Post::factory()->for($thread)->create();

    $recentPost = Post::factory()->for($thread)->create([
        'created_at' => now(),
    ]);

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $recentPost));

    $this->assertModelMissing($recentPost);
});

it('redirects to the thread show page', function () {
    $thread = Thread::factory()->create();

    Post::factory()->for($thread)->create();

    $recentPost = Post::factory()->for($thread)->create([
        'created_at' => now(),
    ]);

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $recentPost))
        ->assertRedirectToRoute('threads.show', $thread->id);
});

it('prevents deleting a post you did not create', function () {
    $thread = Thread::factory()->create();
    $posts = Post::factory(2)->for($thread)->create();
    $lastPost = $posts->sortByDesc('created_at')->first();

    actingAs(User::factory()->create())
        ->delete(route('posts.destroy', $lastPost))
        ->assertForbidden();

    // Double check
    $this->assertModelExists($lastPost);
});

it('prevents deleting a post posted over an hour ago', function () {
    $thread = Thread::factory()->hasPosts(2)->create();

    $this->freezeTime();

    $postToDelete = Post::factory()->for($thread)->create([
        'created_at' => now(),
    ]);

    $this->travel(1)->hour();

    actingAs($postToDelete->user)
        ->delete(route('posts.destroy', $postToDelete))
        ->assertForbidden();
});
