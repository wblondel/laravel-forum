<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Thread extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo<User, Thread>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Post, Thread>
     */
    public function firstPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'first_post_id');
    }

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
    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany('created_at');
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

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function showRoute(array $parameters = []): string
    {
        return route('threads.show', [$this, Str::slug($this->title), ...$parameters]);
    }
}
