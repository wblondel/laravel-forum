<?php

use App\Models\Post;
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
    $post = Post::factory()->withThread()->create();

    post(route('threads.posts.store', $post->thread))
        ->assertRedirectToRoute('login');
});

it('can store a post', function () {
    $user = User::factory()->create();

    $post = Post::factory()->withThread()->create();

    actingAs($user)
        ->post(route('threads.posts.store', $post->thread), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        'thread_id' => $post->thread->id,
        'user_id' => $user->id,
        'body' => $this->validData['body'],
    ]);
});

it('redirects to the last page of the thread', function () {
    $user = User::factory()->create();

    $post = Post::factory()->withThread()->create();

    Post::factory(100)
        ->for($post->thread)
        ->for(User::factory())
        ->recentlyPosted()
        ->create();

    actingAs($user)
        ->post(route('threads.posts.store', $post->thread), $this->validData)
        ->assertRedirect($post->thread->showRoute([
            'page' => $post->thread
                ->refresh()
                ->posts()
                ->paginate(config('pagination.posts_per_page_on_thread'))
                ->lastPage(),
        ]));
});

it('requires data', function (string $attributeToRemove) {
    unset($this->validData[$attributeToRemove]);

    $user = User::factory()->create();

    $post = Post::factory()->withThread()->create();

    actingAs($user)
        ->post(route('threads.posts.store', $post->thread), $this->validData)
        ->assertInvalid($attributeToRemove);
})->with([
    'removing body' => 'body',
]);

it('requires valid data', function (array $badData, array|string $errors) {
    $user = User::factory()->create();

    $post = Post::factory()->withThread()->create();

    actingAs($user)
        ->post(route('threads.posts.store', $post->thread), [...$this->validData, ...$badData])
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
