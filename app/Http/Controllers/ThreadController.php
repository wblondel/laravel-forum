<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\ThreadResource;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|ResponseFactory|RedirectResponse
    {
        Gate::authorize('viewAny', Thread::class);

        // TODO: Make sure it is an INT by using https://github.com/ash-jc-allen/laravel-config-validator
        /** @var int $perPage */
        $perPage = config('pagination.threads_per_page');

        $threads = ThreadResource::collection(Thread::query()
            ->whereHas('posts')
            ->with([
                'user:id,name,profile_photo_path',
                'latestPost.user:id,name,profile_photo_path',
            ])
            ->orderByLatestPost()
            ->withCount('posts')
            ->paginate($perPage)
        );

        /* @phpstan-ignore-next-line */
        if ($threads->resource->currentPage() > $threads->resource->lastPage()) {
            /* @phpstan-ignore-next-line */
            return redirect($threads->resource->url($threads->resource->lastPage()), 302);
        }

        return inertia('Threads/Index', [
            'threads' => $threads,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): ResponseFactory|Response
    {
        Gate::authorize('create', Thread::class);

        return inertia('Threads/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThreadRequest $request): RedirectResponse
    {
        Gate::authorize('create', Thread::class);

        $threadData = $request->safe()->only(['title']);
        $postData = $request->safe()->only(['body']);

        $thread = DB::transaction(function () use ($threadData, $postData, $request) {
            // We first create the thread
            /** @var User $user */ // we know it's a User because the route is for authenticated users only
            $user = $request->user();

            $thread = Thread::create(array_merge($threadData, ['user_id' => $user->id]));

            // Then the post
            $post = Post::create($postData);
            $post->user()->associate($request->user());
            $post->thread()->associate($thread);
            $post->save();

            // Finally we correctly set the firstPost of the thread
            $thread->firstPost()->associate($post);
            $thread->save();

            return $thread;
        });

        return redirect($thread->showRoute());
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Thread $thread): ResponseFactory|Response|RedirectResponse
    {
        Gate::authorize('view', $thread);

        if (! Str::endsWith($thread->showRoute(), $request->path())) {
            return redirect($thread->showRoute((array) $request->query()), 301);
        }

        // TODO: Make sure it is an INT by using https://github.com/ash-jc-allen/laravel-config-validator
        /** @var int $perPage */
        $perPage = config('pagination.posts_per_page_on_thread');

        $posts = PostResource::collection(
            $thread
                ->posts()
                ->with('user')
                ->oldest()
                ->oldest('id')
                ->paginate($perPage)
        );

        /* @phpstan-ignore-next-line */
        if ($posts->resource->currentPage() > $posts->resource->lastPage()) {
            /* @phpstan-ignore-next-line */
            return redirect($posts->resource->url($posts->resource->lastPage()), 302);
        }

        return inertia('Threads/Show', [
            'thread' => fn () => ThreadResource::make($thread->load('user')),
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Thread $thread): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThreadRequest $request, Thread $thread): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Thread $thread): void
    {
        //
    }
}
