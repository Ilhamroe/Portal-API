<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'comment_content' => $this->comment_content,
            'created_at' => date_format($this->created_at,"Y-m-d H:i:s"),
            'commenter' => $this->whenLoaded('commenter'),
        ];
    }
}
