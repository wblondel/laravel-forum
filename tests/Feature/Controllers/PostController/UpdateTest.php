<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {
    $post = Post::factory()->create();

    put(route('posts.update', $post))
        ->assertRedirectToRoute('login');
});

it('can update a post', function () {
    $post = Post::factory()->create([
        'body' => 'This body should be updated.',
    ]);

    $newBody = 'This is the new body.';

    actingAs($post->user)
        ->put(route('posts.update', $post), ['body' => $newBody]);

    $this->assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'body' => $newBody,
    ]);
});

it('redirects to the thread show page', function () {
    $post = Post::factory()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), ['body' => 'This is the new body.'])
        ->assertRedirectToRoute('threads.show', $post->thread);
});

it('redirects to the correct page of posts', function () {
    $post = Post::factory()->create();

    actingAs($post->user)
        ->put(route('posts.update', ['post' => $post, 'page' => 2]), ['body' => 'This is the new body.'])
        ->assertRedirectToRoute('threads.show', ['thread' => $post->thread, 'page' => 2]);
});

it('cannot update a post from another user', function () {
    $post = Post::factory()->create();

    actingAs(User::factory()->create())
        ->put(route('posts.update', $post), ['body' => 'This is the new body.'])
        ->assertForbidden();
});

it('requires a body', function () {
    $post = Post::factory()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), [])
        ->assertInvalid('body');
});

it('requires a valid body', function (mixed $value) {
    $post = Post::factory()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), [
            'body' => $value,
        ])
        ->assertInvalid('body');
})->with([
    '',
    true,
    1,
    null,
    [[]],
    1.28,
]);
