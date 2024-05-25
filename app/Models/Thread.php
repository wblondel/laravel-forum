<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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
    public function author(): HasOneThrough
    {
        return $this->hasOneThrough(
            User::class,
            Post::class,
            'thread_id', // Foreign key on the posts table
            'id', // Foreign key on the users table
            'id', // Local key on the threads table
            'user_id' // Local key on the posts table
        )->oldest('posts.created_at');
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
        )->latest('posts.created_at');
    }
}
