<?php

use App\Models\Post;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('requires authentication', function () {
    delete(route('posts.destroy', Post::factory()->withThread()->create()))
        ->assertRedirectToRoute('login');
});

it('can delete a recent post', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $recentPost = $posts['recent_post'];

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $recentPost));

    $this->assertModelMissing($recentPost);
});

it('redirects to the thread show page', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $recentPost = $posts['recent_post'];
    $firstPost = $posts['first_post'];

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $recentPost))
        ->assertRedirect($firstPost->thread->showRoute());
});

it('redirects to the thread show page with the page query parameter', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $recentPost = $posts['recent_post'];
    $firstPost = $posts['first_post'];

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', ['post' => $recentPost, 'page' => 2]))
        ->assertRedirect($firstPost->thread->showRoute(['page' => 2]));
});

it('prevents deleting a post you did not create', function () {
    $posts = createThreadWithPostAndAdditionalRecentPost();
    $recentPost = $posts['recent_post'];
    $firstPost = $posts['first_post'];

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $firstPost))
        ->assertForbidden();

    // Double check
    $this->assertModelExists($firstPost);
});

it('prevents deleting a post posted over an hour ago', function () {
    $this->freezeTime();

    $posts = createThreadWithPostAndAdditionalRecentPost();
    $recentPost = $posts['recent_post'];

    $this->travel(1)->hour();

    actingAs($recentPost->user)
        ->delete(route('posts.destroy', $recentPost))
        ->assertForbidden();
});
