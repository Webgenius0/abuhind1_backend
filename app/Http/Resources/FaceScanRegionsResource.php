<?php

namespace App\Http\Resources;

use App\Support\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaceScanRegionsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isBlur = ! $request->user()->isPremium();

        $withGrade = fn (?array $rows) => $rows === null ? null : array_map(
            fn ($row) => $row + ['grade' => Grade::from($row['score'] ?? null)],
            $rows
        );

        return [
            'id'            => $this->id,
            'is_blur'       => $isBlur,
            'front_regions' => $withGrade($this->front_regions),
            'side_regions'  => $withGrade($this->side_regions),
            'key_strengths' => $withGrade($this->key_strengths),
        ];
    }
}
