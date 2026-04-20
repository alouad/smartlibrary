<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => (int) $this->rating,
            'review' => $this->review,
            'reader' => [
                'id' => $this->reader->id,
                'name' => $this->reader->name,
                'avatar' => $this->reader->avatar ? asset('storage/' . $this->reader->avatar) : null,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
