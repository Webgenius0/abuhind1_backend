<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FaceScanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $features = ($this->jawline !== null && $this->cheekbones !== null && $this->nose !== null)
            ? round(($this->jawline + $this->cheekbones + $this->nose) / 3, 1)
            : null;

        $overallScore = ($this->harmony_score !== null && $features !== null
                && $this->angularity !== null && $this->dimorphism !== null)
            ? round(($this->harmony_score + $features + $this->angularity + $this->dimorphism) / 4, 1)
            : null;

        return [
            'id'              => $this->id,
            'status'          => $this->status,
            'front_image_url' => $this->front_image_path ? Storage::disk('public')->url($this->front_image_path) : null,
            'side_image_url'  => $this->side_image_path ? Storage::disk('public')->url($this->side_image_path) : null,

            'harmony_score' => $this->harmony_score,
            'symmetry'      => $this->symmetry,
            'jawline'       => $this->jawline,
            'cheekbones'    => $this->cheekbones,
            'nose'          => $this->nose,
            'skin_quality'  => $this->skin_quality,
            'angularity'    => $this->angularity,
            'dimorphism'    => $this->dimorphism,
            'overall_score' => $overallScore,

            'facial_width_height_ratio' => $this->facial_width_height_ratio,
            'key_strengths'             => $this->key_strengths,
            'regions'                   => $this->regions,
            'front_regions'             => $this->front_regions,
            'side_regions'              => $this->side_regions,
            'summary'                   => $this->summary,
            'report_pdf_url'            => $this->report_pdf_url,

            // null if no card has been generated for this scan yet
            'card' => $this->whenLoaded('card', fn () => $this->card ? [
                'harmony'    => $this->card->harmony,
                'features'   => $this->card->features,
                'angularity' => $this->card->angularity,
                'dimorphism' => $this->card->dimorphism,
                'overall'    => $this->card->overall,
                'pdf_url'    => $this->card->pdf_url,
            ] : null),

            'created_at' => $this->created_at,
        ];
    }
}
