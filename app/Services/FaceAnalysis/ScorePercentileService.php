<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;

class ScorePercentileService
{
    public function for(FaceScan $scan): array
    {
        $version = $scan->score_version ?? config('scoring.version');
        $min     = config('scoring.min_sample');

        // প্রতি ইউজারের সর্বশেষ সফল scan, শুধু এই version-এর
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
                'population'   => 'Malameh users',
            ];
        }

        $below = (clone $base)->where('overall_score', '<', $scan->overall_score)->count();

        $histogram = (clone $base)
            ->selectRaw('FLOOR(overall_score * 2) / 2 as bin, COUNT(*) as n')
            ->groupBy('bin')->orderBy('bin')
            ->get()
            ->map(fn ($r) => ['from' => (float) $r->bin, 'to' => (float) $r->bin + 0.5, 'count' => (int) $r->n])
            ->values();

        return [
            'available'   => true,
            'population'  => 'Malameh users',
            'sample_size' => $total,
            'user_score'  => (float) $scan->overall_score,
            'percentile'  => (int) round($below / $total * 100),
            'histogram'   => $histogram,
        ];
    }
}
