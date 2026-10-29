<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DynamicGalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'type'        => $this->type,
            'title'       => $this->title,
            'subtitle'    => $this->subtitle,
            'description' => $this->description,
            'right_image' => $this->right_image,
            'wrong_image' => $this->wrong_image,
            'right_label' => $this->right_label,
            'wrong_label' => $this->wrong_label,
        ];
    }
}
