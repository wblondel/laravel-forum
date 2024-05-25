<?php

namespace App\Http\Resources;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
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
            'name' => $this->name,
            //'email' => $this->when($this->id === $request->user()?->id, $this->email),
            'profile_photo_url' => $this->profile_photo_url,
            'created_threads' => ThreadResource::collection($this->whenLoaded('createdThreads')),
            'created_at' => $this->when($request->routeIs('users.index'), function() {
                return $this->created_at;
            }),
           // 'updated_at' => $this->updated_at,
        ];
    }
}
