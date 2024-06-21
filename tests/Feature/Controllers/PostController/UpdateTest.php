<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

beforeEach(function () {
    $this->validData = [
        'body' => 'Hello everyone! This is an awesome post, and I am very happy to be part of this community! ' .
            'I hope I will have a lot of fun.',
    ];
});

it('requires authentication', function () {
    $post = Post::factory()->withThread()->create();

    put(route('posts.update', $post))
        ->assertRedirectToRoute('login');
});

it('can update a post', function () {
    $post = Post::factory()->withThread()->create([
        'body' => 'This body should be updated.',
    ]);

    actingAs($post->user)
        ->put(route('posts.update', $post), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        'id' => $post->id,
        'body' => $this->validData['body'],
    ]);
});

it('redirects to the thread show page', function () {
    $post = Post::factory()->withThread()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), $this->validData)
        ->assertRedirect($post->thread->showRoute());
});

it('redirects to the correct page of posts', function () {
    $post = Post::factory()->withThread()->create();

    actingAs($post->user)
        ->put(route('posts.update', ['post' => $post, 'page' => 2]), $this->validData)
        ->assertRedirect($post->thread->showRoute(['page' => 2]));
});

it('cannot update a post from another user', function () {
    $post = Post::factory()->withThread()->create();

    actingAs(User::factory()->create())
        ->put(route('posts.update', $post), $this->validData)
        ->assertForbidden();
});

it('requires data', function (string $attributeToRemove) {
    unset($this->validData[$attributeToRemove]);

    $post = Post::factory()->withThread()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), $this->validData)
        ->assertInvalid($attributeToRemove);
})->with([
    'removing body' => 'body',
]);

it('requires valid data', function (array $badData, array|string $errors) {
    $post = Post::factory()->withThread()->create();

    actingAs($post->user)
        ->put(route('posts.update', $post), [...$this->validData, ...$badData])
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
