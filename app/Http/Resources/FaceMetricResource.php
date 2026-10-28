<?php

namespace App\Http\Resources;

use App\Support\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaceMetricResource extends JsonResource
{
    /**
     * Transform the face metric into an API response array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Extract array representation of metric data
        $d = is_array($this->resource) ? $this->resource : (array) $this->resource;

        // Metric score evaluation (0.0 - 10.0 scale)
        $score = isset($d['score']) ? (float) $d['score'] : 0.0;

        // Grade object derived from score (key and Arabic label)
        $grade = $d['grade'] ?? Grade::from($score);

        // Visual overlay coordinates strictly formatted (angle, badge, horizontal, vertical)
        $overlay = isset($d['overlay']) ? [
            'angle'      => isset($d['overlay']['angle']) ? (float) $d['overlay']['angle'] : null,
            'badge'      => $d['overlay']['badge'] ?? null,
            'horizontal' => $d['overlay']['horizontal'] ?? null,
            'vertical'   => $d['overlay']['vertical'] ?? null,
        ] : null;

        return [
            // Unique metric identifier key
            'key'         => $d['key'] ?? null,

            // Camera perspective: 'front' or 'side'
            'view'        => $d['view'] ?? 'front',

            // Anatomical facial zone
            'zone'        => $d['zone'] ?? null,

            // Localized display title
            'title'       => $d['title'] ?? null,

            // Standard measurement unit (x, mm, °, %, ratio)
            'unit'        => $d['unit'] ?? null,

            // Measured numerical value
            'value'       => isset($d['value']) ? (float) $d['value'] : null,

            // Evaluated proportion score out of 10.0
            'score'       => $score,

            // Evaluated quality grade object
            'grade'       => $grade,

            // Minimum scale boundary
            'scale_min'   => isset($d['scale_min']) ? (float) $d['scale_min'] : null,

            // Maximum scale boundary
            'scale_max'   => isset($d['scale_max']) ? (float) $d['scale_max'] : null,

            // Minimum ideal boundary
            'ideal_min'   => isset($d['ideal_min']) ? (float) $d['ideal_min'] : null,

            // Maximum ideal boundary
            'ideal_max'   => isset($d['ideal_max']) ? (float) $d['ideal_max'] : null,

            // Diagnostic metric description
            'description' => $d['description'] ?? null,

            // AI generated diagnostic clinical note
            'note'        => $d['note'] ?? null,

            // 1-based sequential position index in metric list
            'position'    => isset($d['position']) ? (int) $d['position'] : null,

            // Total count of metrics available in this scan
            'total'       => isset($d['total']) ? (int) $d['total'] : null,

            // Visual measurement overlay coordinates for face photo
            'overlay'     => $overlay,
        ];
    }
}
