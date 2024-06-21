<?php

namespace App\Http\Resources;

use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Thread
 */
class ThreadResource extends JsonResource
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
            'first_post' => PostResource::make($this->whenLoaded('firstPost')),
            'latest_post' => PostResource::make($this->whenLoaded('latestPost')),
            'posts_count' => $this->when(isset($this->posts_count), function () {
                return $this->posts_count;
            }),
            'title' => $this->title,
            'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            'routes' => [
                'show' => $this->showRoute(),
            ],
        ];
    }
}
