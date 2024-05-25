<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreThreadRequest;
use App\Http\Requests\UpdateThreadRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Thread;
use Inertia\Response;
use Inertia\ResponseFactory;

class ThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|ResponseFactory
    {
        $threads = ThreadResource::collection(Thread::query()
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
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreThreadRequest $request): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Thread $thread): void
    {
        //
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