<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaceScanRegionsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isBlur = ! $request->user()->isPremium();

        return [
            'id'            => $this->id,
            'is_blur'       => $isBlur,
            'front_regions' => $this->front_regions,
            'side_regions'  => $this->side_regions,
        ];
    }
}
