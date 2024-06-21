<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\ThreadResource;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Response;
use Inertia\ResponseFactory;

/*
 * TODO: I know that the first post appears twice. I will modify the relations between Thread and Post later.
 *  My goal is to make a clone of phpBB, because nostalgia :-), but for now I'm following a Laracasts course.
 */
class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|ResponseFactory
    {
        Gate::authorize('viewAny', Thread::class);

        $threads = ThreadResource::collection(Thread::query()
            //->whereHas('posts', function ($query) {
            //    $query->where('created_at', '>=', now()->subDay());
            //})
            ->with([
                'firstPost.user:id,name,profile_photo_path',
                'latestPost.user:id,name,profile_photo_path',
            ])
            ->orderByLatestPost()
            ->withCount('posts')
            ->paginate()
        );

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
    public function store(StoreThreadRequest $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create', Thread::class);

        $threadData = $request->safe()->only(['title']);
        $postData = $request->safe()->only(['body']);

        $thread = DB::transaction(function () use ($threadData, $postData, $request) {
            $thread = Thread::create($threadData);

            (new Post($postData))
                ->user()->associate($request->user())
                ->thread()->associate($thread)
                ->save();

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

        return inertia('Threads/Show', [
            'thread' => fn () => ThreadResource::make($thread->load('firstPost.user')),
            'posts' => fn () => PostResource::collection(
                $thread
                    ->posts()
                    ->with('user')
                    ->oldest()
                    ->oldest('id')
                    ->paginate()
            ),
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
