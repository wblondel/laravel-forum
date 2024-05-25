<?php

use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Inertia\Testing\AssertableInertia;

use function Pest\Laravel\get;

it('should return the correct component', function () {
    get(route('threads.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->component('Threads/Index', true)
        );
});

it('passes threads to the view', function () {
    AssertableInertia::macro('hasResource', function (string $key, JsonResource $resource) {
        $props = $this->toArray()['props'];

        $compiledResource = $resource->response()->getData(true);

        expect($props)
            ->toHaveKey($key, message: "Key \"{$key}\" not passed as a property to Inertia.")
            ->and($props[$key])
            ->toEqual($compiledResource);

        return $this;
    });

    AssertableInertia::macro('hasPaginatedResource', function (string $key, ResourceCollection $resourceCollection) {
        $props = $this->toArray()['props'];

        $compiledResource = $resourceCollection->response()->getData(true);

        expect($props)
            ->toHaveKey($key, message: "Key \"{$key}\" not passed as a property to Inertia.")
            ->and($props[$key])
            ->toHaveKeys(['data', 'links', 'meta'])
            ->data
            ->toEqual($compiledResource['data']);

        return $this;
    });

    $threads = Thread::factory(5)->hasPosts(10)->create();

    $paginatedResource = ThreadResource::collection($threads
        ->load([
            'firstPost.user:id,name,profile_photo_path',
            'latestPost.user:id,name,profile_photo_path',
        ])
        ->sortByDesc(function ($thread) {
            return $thread->latestPost->created_at;
        })
        ->loadCount('posts')
    );

    get(route('threads.index'))
        ->assertInertia(fn (AssertableInertia $inertia) => $inertia
            ->hasPaginatedResource('threads', $paginatedResource)
        );
});
