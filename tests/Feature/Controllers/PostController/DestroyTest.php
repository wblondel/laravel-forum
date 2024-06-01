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

it('can delete a post', function () {
    $thread = Thread::factory()->create();
    $posts = Post::factory(2)->for($thread)->create();

    $secondPost = $posts->sortByDesc('created_at')->first();

    actingAs($secondPost->user)
        ->delete(route('posts.destroy', $secondPost));

    $this->assertModelMissing($secondPost);
});

it('redirects to the thread show page', function () {
    $thread = Thread::factory()->create();
    $posts = Post::factory(2)->for($thread)->create();

    $lastPost = $posts->sortByDesc('created_at')->first();

    actingAs($lastPost->user)
        ->delete(route('posts.destroy', $lastPost))
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
