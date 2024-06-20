<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = [
        'body' => 'Hello everyone! This is an awesome post, and I am very happy to be part of this community! ' .
            'I hope I will have a lot of fun.',
    ];
});

it('requires authentication', function () {
    $thread = Thread::factory()->hasPosts(1)->create();

    post(route('threads.posts.store', $thread))
        ->assertRedirectToRoute('login');
});

it('can store a post', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        'thread_id' => $thread->id,
        'user_id' => $user->id,
        'body' => $this->validData['body'],
    ]);
});

it('redirects to the thread show page', function () {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), $this->validData)
        ->assertRedirect($thread->showRoute());
});

it('requires data', function (string $attributeToRemove) {
    unset($this->validData[$attributeToRemove]);

    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), $this->validData)
        ->assertInvalid($attributeToRemove);
})->with([
    'removing body' => 'body',
]);

it('requires valid data', function (array $badData, array|string $errors) {
    $user = User::factory()->create();
    $thread = Thread::factory()->hasPosts(1)->create();

    actingAs($user)
        ->post(route('threads.posts.store', $thread), [...$this->validData, ...$badData])
        ->assertInvalid($errors);
})->with([
    [['body' => ''], 'body'],
    [['body' => true], 'body'],
    [['body' => 1], 'body'],
    [['body' => null], 'body'],
    [['body' => []], 'body'],
    [['body' => 1.28], 'body'],
    [['body' => str_repeat('a', 99)], 'body'],
    [['body' => str_repeat('a', 10_001)], 'body'],
]);
