<?php

namespace App\Http\Resources;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => UserResource::make($this->whenLoaded('user')),
            'thread' => ThreadResource::make($this->whenLoaded('thread')),
            'body' => $this->when(
                ! $request->routeIs('threads.index'),
                $this->body
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'can' => [
                'delete' => $request->user()?->can('delete', $this->resource),
            ],
        ];
    }
}
