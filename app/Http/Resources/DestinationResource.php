<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DestinationResource extends JsonResource
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
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'category' => $this->category,
            'average_rating' => $this->average_rating,
            'image_url' => $this->image_url,
            'approx_price_range' => $this->approx_price_range,
            'best_time_to_visit' => $this->best_time_to_visit,
            'tags' => $this->tags->pluck('tag_name')
        ];
    }
}
