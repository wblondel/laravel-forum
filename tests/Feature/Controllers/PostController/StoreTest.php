<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

use function Pest\Laravel\actingAs;

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

it('cannot store a post with a null body', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [
            'body' => null,
        ])
        ->assertSessionHasErrors('body');

});

it('cannot store a post with an empty body', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [
            'body' => '',
        ])
        ->assertSessionHasErrors('body');

});

it('cannot store a post with a missing body', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [])
        ->assertSessionHasErrors('body');

});

it('redirects to the thread show page', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [
            'body' => 'This is a post.',
        ])
        ->assertRedirect(route('threads.show', $thread));
});
