<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'published_date' => $this->published_date,
            'isbn' => $this->isbn,
            'page_count' => $this->page_count,
            'language' => $this->language,
            'views_count' => $this->views_count,
            'average_rating' => round($this->average_rating, 1),
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
            ],
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
