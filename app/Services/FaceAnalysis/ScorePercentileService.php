<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;

class ScorePercentileService
{
    public function for(FaceScan $scan): array
    {
        $version = $scan->score_version ?? config('scoring.version');
        $min     = (int) config('scoring.min_sample');

        $base = FaceScan::query()
            ->whereNotNull('overall_score')
            ->whereIn('id', fn ($q) => $q->selectRaw('MAX(id)')
                ->from('face_scans')
                ->where('status', 'processed')
                ->where('score_version', $version)
                ->groupBy('user_id'));

        $total = (clone $base)->count();

        if ($total < $min || $scan->overall_score === null) {
            return [
                'available'    => false,
                'sample_size'  => $total,
                'min_required' => $min,
                'population'   => __('api.population'),
                'message'      => __('api.not_enough_data', ['count' => $total, 'min' => $min]),
            ];
        }

        $score = (float) $scan->overall_score;

        $below = (clone $base)->where('overall_score', '<', $score)->count();
        $equal = (clone $base)->where('overall_score', $score)->count();

        $percentile = (int) round(($below + 0.5 * $equal) / $total * 100);
        $percentile = max(1, min(99, $percentile));
        $top        = 100 - $percentile;

        $summary = match (true) {
            $top <= 10 => __('api.band_top', ['top' => $top]),
            $top <= 30 => __('api.band_high', ['top' => $top]),
            $top <= 70 => __('api.band_average'),
            default    => __('api.band_low'),
        };

        $counts = (clone $base)
            ->selectRaw('LEAST(FLOOR(overall_score * 2), 19) as bin, COUNT(*) as n')
            ->groupBy('bin')
            ->pluck('n', 'bin');

        $histogram = collect(range(0, 19))->map(fn ($i) => [
            'from'  => $i / 2,
            'to'    => ($i + 1) / 2,
            'count' => (int) ($counts[$i] ?? 0),
        ])->values();

        return [
            'available'   => true,
            'population'  => __('api.population'),
            'sample_size' => $total,
            'user_score'  => $score,
            'percentile'  => $percentile,
            'top_percent' => $top,
            'summary'     => $summary,
            'histogram'   => $histogram,
        ];
    }
}
