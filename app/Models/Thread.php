<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Facades\DB;

class Thread extends Model
{
    use HasFactory;

    /**
     * @return HasMany<Post>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * @return HasOne<Post>
     */
    public function firstPost(): HasOne
    {
        return $this->hasOne(Post::class)->oldestOfMany('created_at');
    }

    /**
     * @return HasOne<Post>
     */
    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany('created_at');
    }

    /**
     * @return HasOneThrough<User>
     */
    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Post::class,
            'thread_id', // Foreign key on the posts table
            'id', // Foreign key on the users table
            'id', // Local key on the threads table
            'user_id' // Local key on the posts table
        )->oldest('created_at');
    }

    /**
     * @return HasOneThrough<User>
     */
    public function latestPostUser(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Post::class,
            'thread_id',
            'id',
            'id',
            'user_id'
        )->latest('created_at');
    }

    /**
     * @param  Builder<Thread>  $query
     * @return Builder<Thread>
     */
    public function scopeOrderByLatestPost(Builder $query): Builder
    {
        return $query
            ->with('latestPost')
            ->withCount(['posts as latest_post_created_at' => function (Builder $query) {
                $query->select(DB::raw('MAX(created_at)'));
            }])
            ->orderByDesc('latest_post_created_at');
    }
}
