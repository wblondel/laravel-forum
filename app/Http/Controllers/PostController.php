<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\Thread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request, Thread $thread): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        (new Post($request->validated()))
            ->user()->associate($request->user())
            ->thread()->associate($thread)
            ->save();

        return redirect($thread->showRoute())
            ->banner('Post successfully published.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $validated = $request->validated();

        $post->update($validated);

        $thread = $post->thread;

        if (is_null($thread)) {
            abort(404);
        }

        return redirect($thread->showRoute(['page' => $request->query('page')]))
            ->banner('Post successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        $thread = $post->thread;

        if (is_null($thread)) {
            abort(404);
        }

        return redirect($thread->showRoute(['page' => $request->query('page')]))
            ->banner('Post successfully deleted.');
    }
}
