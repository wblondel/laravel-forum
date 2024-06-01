<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

it('requires authentication', function () {
    $thread = Thread::factory()->hasPosts(1)->create();

    post(route('threads.posts.store', $thread))
        ->assertRedirectToRoute('login');
});

it('can store a post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)->post(route('threads.posts.store', $thread), [
        'body' => 'This is a post.',
    ]);

    $this->assertDatabaseHas(Post::class, [
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'body' => 'This is a post.',
    ]);
});

it('requires a body', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [])
        ->assertInvalid('body');
});

it('requires a valid body', function (mixed $value) {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [
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

it('redirects to the thread show page', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [
            'body' => 'This is a post.',
        ])
        ->assertRedirect(route('threads.show', $thread));
});
