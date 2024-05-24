<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Thread, Post>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * @return BelongsTo<User, Post>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * When a Post is an answer to another Post, it has a parent.
     *
     * @return BelongsTo<Post, Post>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * When a Post has been answered by other Posts, it has replies.
     *
     * @return HasMany<Post>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
