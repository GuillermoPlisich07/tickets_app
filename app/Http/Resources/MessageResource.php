<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => $this->id,
            'content' => $this->content,
            'author' => $this->author,
            'author_type' => $this->author_type,
            'is_root' => $this->is_root,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}
