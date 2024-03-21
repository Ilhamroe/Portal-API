<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'news_content' => $this->news_content,
            'created_at' => date_format($this->created_at, "Y-m-d H:i:s"),
            // 'author' => $this->author,
            'writer' => $this->whenLoaded('writer'),
            'commenter' => $this->whenLoaded('commenter', function () {
                return collect($this->commenter)->each(function ($comment) {
                    $comment->commenter;
                    return $comment;
                });
            }),
            'total_commenter' => $this->whenLoaded('commenter', function () {
                return $this->commenter->count();
            })
        ];
    }
}
