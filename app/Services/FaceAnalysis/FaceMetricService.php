<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\Gemini\GeminiMetricService;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FaceMetricService
{
    public function __construct(
        protected GeminiMetricService $gemini,
        protected FaceScanRepositoryInterface $faceScans,
    ) {
    }

    /** @return array<int, array<string, mixed>> */
    public function forScan(FaceScan $scan): array
    {
        $stored = $scan->metrics;

        if (empty($stored)) {
            $stored = $this->generate($scan);
        }

        $out = [];

        // config-এর ক্রমই modal-এর ক্রম (1/33, 2/33 ...)
        foreach (config('face_metrics', []) as $key => $m) {
            if (! isset($stored[$key])) {
                continue;
            }

            $out[] = [
                'key'         => $key,
                'view'        => $m['view'] ?? 'front',
                'zone'        => $m['zone'] ?? null,
                'title'       => $m['title'] ?? $key,
                'unit'        => $m['unit'] ?? null,
                'value'       => $stored[$key]['value'],
                'score'       => $stored[$key]['score'],
                'scale_min'   => $m['scale_min'] ?? null,
                'scale_max'   => $m['scale_max'] ?? null,
                'ideal_min'   => $m['ideal_min'] ?? null,
                'ideal_max'   => $m['ideal_max'] ?? null,
                'description' => $m['description'] ?? null,
            ];
        }

        return $out;
    }

    protected function generate(FaceScan $scan): array
    {
        $images = ['front' => Storage::disk('public')->path($scan->front_image_path)];

        if ($scan->side_image_path) {
            $images['side'] = Storage::disk('public')->path($scan->side_image_path);
        }

        $values = $this->gemini->measure($images);

        if (! $values) {
            throw new RuntimeException('No metrics could be measured for this scan.');
        }

        $stored = [];

        foreach ($values as $key => $value) {
            $stored[$key] = [
                'value' => (float) $value,
                'score' => $this->score((float) $value, config("face_metrics.{$key}")),
            ];
        }

        $this->faceScans->update($scan, ['metrics' => $stored]);

        return $stored;
    }

    /** ideal range-এর ভেতরে হলে 10, বাইরে গেলে scale-এর প্রান্তের দিকে ধীরে ০-তে নামে */
    protected function score(float $value, array $m): float
    {
        $min = (float) ($m['ideal_min'] ?? $value);
        $max = (float) ($m['ideal_max'] ?? $value);

        if ($value >= $min && $value <= $max) {
            return 10.0;
        }

        $below = $value < $min;
        $dist  = $below ? $min - $value : $value - $max;
        $span  = $below
            ? $min - (float) ($m['scale_min'] ?? $min - 1)
            : (float) ($m['scale_max'] ?? $max + 1) - $max;

        return round(max(0, 10 * (1 - $dist / max($span, 0.0001))), 1);
    }
}
