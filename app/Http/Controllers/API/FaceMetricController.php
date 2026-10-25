<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\FaceAnalysis\FaceMetricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaceMetricController extends Controller
{
    public function __construct(
        protected FaceScanRepositoryInterface $faceScans,
        protected FaceMetricService $metrics,
    ) {
    }

    public function index(Request $request, int $faceScan): JsonResponse
    {
        set_time_limit(180); // Initial Gemini API call may take additional processing time

        $items = $this->metrics->forScan($this->authorizedScan($request, $faceScan));

        return response()->json([
            'message' => 'Face metrics.',
            'data'    => [
                'total'   => count($items),
                'metrics' => $items,
            ],
        ]);
    }

    public function show(Request $request, int $faceScan, string $key): JsonResponse
    {
        set_time_limit(180);

        $items = $this->metrics->forScan($this->authorizedScan($request, $faceScan));
        $index = array_search($key, array_column($items, 'key'), true);

        abort_if($index === false, 404, 'Metric not found.');

        return response()->json([
            'message' => 'Face metric.',
            'data'    => $items[$index] + ['position' => $index + 1, 'total' => count($items)],
        ]);
    }

    protected function authorizedScan(Request $request, int $faceScan)
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);
        abort_unless($scan->status === 'processed', 422, 'Scan is not ready yet.');

        return $scan;
    }
}
