<?php

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = [
        'title' => 'Hello World',
        'body' => 'Hello everyone! This is my very first post, and I am very happy to be part of this community! ' .
            'I hope I will have a lot of fun.',
    ];
});

it('requires authentication', function () {
    post(route('threads.store'))->assertRedirectToRoute('login');
});

it('can store a thread', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('threads.store'), $this->validData);

    $this->assertDatabaseHas(Thread::class, [
        'title' => $this->validData['title'],
    ]);

    $this->assertDatabaseHas(Post::class, [
        'body' => $this->validData['body'],
        'user_id' => $user->id,
        'thread_id' => 1,
    ]);
});

it('redirects to the thread show page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('threads.store'), $this->validData)
        ->assertRedirect(Thread::latest('id')->first()->showRoute());
});

it('requires data', function (string $attributeToRemove) {
    unset($this->validData[$attributeToRemove]);

    actingAs(User::factory()->create())
        ->post(route('threads.store'), $this->validData)
        ->assertInvalid($attributeToRemove);
})->with([
    'removing body' => 'body',
    'removing title' => 'title',
]);

it('requires valid data', function (array $badData, array|string $errors) {
    actingAs(User::factory()->create())
        ->post(route('threads.store'), [...$this->validData, ...$badData])
        ->assertInvalid($errors);
})->with([
    // Bad title
    [['title' => ''], 'title'],
    [['title' => true], 'title'],
    [['title' => 1], 'title'],
    [['title' => null], 'title'],
    [['title' => []], 'title'],
    [['title' => 1.28], 'title'],
    'Title too short' => [['title' => str_repeat('a', 9)], 'title'],
    'Title too long' => [['title' => str_repeat('a', 121)], 'title'],
    // Bad body
    [['body' => ''], 'body'],
    [['body' => true], 'body'],
    [['body' => 1], 'body'],
    [['body' => null], 'body'],
    [['body' => []], 'body'],
    [['body' => 1.28], 'body'],
    'Body too short' => [['body' => str_repeat('a', 99)], 'body'],
    'Body too long' => [['body' => str_repeat('a', 10_001)], 'body'],
]);
