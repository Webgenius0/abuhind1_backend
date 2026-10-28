<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\Gemini\GeminiMetricService;
use App\Support\Grade;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class FaceMetricService
{
    // Inject Gemini AI and Face Scan repository dependencies
    public function __construct(
        protected GeminiMetricService $gemini,
        protected FaceScanRepositoryInterface $faceScans,
    ) {
    }

    /**
     * Compute full list of facial metrics for the given scan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function forScan(FaceScan $scan): array
    {
        // Retrieve cached metrics if previously saved on scan model
        $stored = $scan->metrics ?? [];

        // Index front region metrics by key for quick lookup
        $frontMap = collect($scan->front_regions ?? [])
            ->filter(fn ($r) => ! empty($r['metric']))
            ->keyBy('metric')
            ->all();

        // Index side region metrics by key for quick lookup
        $sideMap = collect($scan->side_regions ?? [])
            ->filter(fn ($r) => ! empty($r['metric']))
            ->keyBy('metric')
            ->all();

        // Check if side profile perspective is available for this scan
        $hasSide = ! empty($scan->side_image_path) || ! empty($scan->side_regions);

        // Fallback to Gemini measurement if scan lacks both stored metrics and region analysis
        if (empty($stored) && empty($frontMap) && empty($sideMap)) {
            $stored = $this->generate($scan);
        }

        // Filter configuration based on available camera angles
        $definitions = collect(config('face_metrics', []))
            ->filter(fn ($m) => $hasSide || ($m['view'] ?? 'front') === 'front')
            ->all();

        $items = [];
        $total = count($definitions);
        $position = 1;

        // Iterate through all configured metric definitions in display order
        foreach ($definitions as $key => $m) {
            // Retrieve regional diagnostic analysis data for this metric
            $region = $frontMap[$key] ?? $sideMap[$key] ?? null;

            // Resolve proportion score out of 10.0
            $score = isset($stored[$key]['score'])
                ? (float) $stored[$key]['score']
                : (isset($region['score']) ? (float) $region['score'] : null);

            // Compute score from fWHR measurement if applicable
            if ($score === null && $key === 'facial_width_height_ratio' && $scan->facial_width_height_ratio !== null) {
                $score = $this->score((float) $scan->facial_width_height_ratio, $m);
            }

            // Default fallback score if not evaluated
            $score = $score !== null ? (float) $score : 7.5;

            // Resolve measured numerical value
            $value = isset($stored[$key]['value'])
                ? (float) $stored[$key]['value']
                : ($key === 'facial_width_height_ratio' && $scan->facial_width_height_ratio !== null
                    ? (float) $scan->facial_width_height_ratio
                    : $this->deriveValueFromScore($score, $m));

            // Retrieve diagnostic clinical note
            $note = $region['note'] ?? null;

            // Construct normalized visual overlay coordinates
            $overlay = $this->buildOverlay($m, $value, $score);

            // Compile standardized metric data payload
            $items[] = [
                'key'         => $key,
                'view'        => $m['view'] ?? 'front',
                'zone'        => $m['zone'] ?? null,
                'title'       => $m['title'] ?? $key,
                'unit'        => $m['unit'] ?? null,
                'value'       => $value,
                'score'       => $score,
                'grade'       => Grade::from($score),
                'scale_min'   => isset($m['scale_min']) ? (float) $m['scale_min'] : null,
                'scale_max'   => isset($m['scale_max']) ? (float) $m['scale_max'] : null,
                'ideal_min'   => isset($m['ideal_min']) ? (float) $m['ideal_min'] : null,
                'ideal_max'   => isset($m['ideal_max']) ? (float) $m['ideal_max'] : null,
                'description' => $m['description'] ?? null,
                'note'        => $note,
                'position'    => $position,
                'total'       => $total,
                'overlay'     => $overlay,
            ];

            $position++;
        }

        return $items;
    }

    /**
     * Locate a single metric by key with its sequential position.
     *
     * @return array<string, mixed>|null
     */
    public function findMetric(FaceScan $scan, string $key): ?array
    {
        // Retrieve complete collection for scan
        $items = $this->forScan($scan);

        // Find index of matching metric key
        $index = array_search($key, array_column($items, 'key'), true);

        // Return matched metric item or null
        return $index !== false ? $items[$index] : null;
    }

    /**
     * Construct normalized visual overlay coordinates and badge text.
     *
     * @param  array<string, mixed>  $m
     * @return array<string, mixed>
     */
    protected function buildOverlay(array $m, float $value, float $score): array
    {
        // Extract configured overlay coordinate template
        $tpl = $m['overlay'] ?? [];

        // Horizontal measurement axis coordinates
        $horizontal = $tpl['horizontal'] ?? null;

        // Vertical measurement axis coordinates
        $vertical = $tpl['vertical'] ?? null;

        // Floating badge display position
        $badgePos = $tpl['badge'] ?? ['x' => 500, 'y' => 500];

        // Measured angle in degrees or null
        $angle = $tpl['angle'] ?? (($m['unit'] ?? '') === '°' ? $value : null);

        // Standard measurement unit
        $unit = $m['unit'] ?? '';

        // Format user-facing badge text based on unit type
        $badgeText = match ($unit) {
            '°'     => round($value, 1) . '°',
            'x'     => round($value, 2) . 'x',
            '%'     => round($value, 0) . '%',
            'mm'    => round($value, 1) . ' mm',
            'ratio' => (string) round($value, 2),
            default => round($score, 1) . ' / 10',
        };

        return [
            // Measured geometric angle in degrees or null
            'angle'      => $angle !== null ? (float) $angle : null,

            // Floating badge label and coordinates
            'badge'      => [
                'text' => $badgeText,
                'x'    => $badgePos['x'],
                'y'    => $badgePos['y'],
            ],

            // Horizontal measurement vector
            'horizontal' => $horizontal,

            // Vertical measurement vector
            'vertical'   => $vertical,
        ];
    }

    /**
     * Derive a realistic numerical value based on the score and ideal scale boundaries.
     *
     * @param  array<string, mixed>  $m
     */
    protected function deriveValueFromScore(float $score, array $m): float
    {
        // Ideal lower boundary
        $idealMin = (float) ($m['ideal_min'] ?? 1.0);

        // Ideal upper boundary
        $idealMax = (float) ($m['ideal_max'] ?? $idealMin);

        // Scale minimum boundary
        $scaleMin = (float) ($m['scale_min'] ?? ($idealMin * 0.8));

        // Calculate value within ideal range for high scores
        if ($score >= 7.0) {
            $ratio = min(1.0, max(0.0, ($score - 7.0) / 3.0));
            return round($idealMin + ($ratio * ($idealMax - $idealMin)), 2);
        }

        // Taper value down toward minimum scale for lower scores
        $deficit = (7.0 - $score) / 7.0;
        return round(max($scaleMin, $idealMin - ($deficit * ($idealMin - $scaleMin))), 2);
    }

    /**
     * Generate raw measurements using Gemini Vision API and cache on scan model.
     *
     * @return array<string, array<string, float>>
     */
    protected function generate(FaceScan $scan): array
    {
        // Front view image path
        $images = ['front' => Storage::disk('public')->path($scan->front_image_path)];

        // Optional side profile image path
        if ($scan->side_image_path) {
            $images['side'] = Storage::disk('public')->path($scan->side_image_path);
        }

        // Request AI landmark measurement
        $values = $this->gemini->measure($images);

        if (! $values) {
            throw new RuntimeException('No metrics could be measured for this scan.');
        }

        $stored = [];

        // Score each measured value against its defined scale
        foreach ($values as $key => $value) {
            $stored[$key] = [
                'value' => (float) $value,
                'score' => $this->score((float) $value, config("face_metrics.{$key}")),
            ];
        }

        // Persist computed metrics to database
        $this->faceScans->update($scan, ['metrics' => $stored]);

        return $stored;
    }

    /**
     * Calculate 0.0 - 10.0 score based on proximity to ideal boundaries.
     *
     * @param  array<string, mixed>  $m
     */
    protected function score(float $value, array $m): float
    {
        // Ideal range minimum
        $min = (float) ($m['ideal_min'] ?? $value);

        // Ideal range maximum
        $max = (float) ($m['ideal_max'] ?? $value);

        // Perfect score if within ideal boundary
        if ($value >= $min && $value <= $max) {
            return 10.0;
        }

        // Calculate distance from closest ideal boundary
        $below = $value < $min;
        $dist  = $below ? $min - $value : $value - $max;

        // Calculate total allowable span to scale edge
        $span  = $below
            ? $min - (float) ($m['scale_min'] ?? $min - 1)
            : (float) ($m['scale_max'] ?? $max + 1) - $max;

        // Return proportional score clamped to zero
        return round(max(0, 10 * (1 - $dist / max($span, 0.0001))), 1);
    }
}
