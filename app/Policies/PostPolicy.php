<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;

class PostPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // TODO: when the feature to lock threads will be implemented
        //  creating a post under locked threads won't be authorized
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        /** @var Carbon $postCreatedAt */
        $postCreatedAt = $post->created_at;

        return
            $user->id === $post->user_id &&
            $postCreatedAt->isAfter(now()->subHour());
    }
}
