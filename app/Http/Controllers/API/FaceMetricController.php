<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaceMetricResource;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\FaceAnalysis\FaceMetricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaceMetricController extends Controller
{
    // Inject scan repository and face metric service dependencies
    public function __construct(
        protected FaceScanRepositoryInterface $faceScans,
        protected FaceMetricService $metrics,
    ) {
    }

    // Return the complete collection of facial metrics for the scan
    public function index(Request $request, int $faceScan): JsonResponse
    {
        set_time_limit(180);

        // Fetch validated face scan record for the authorized user
        $scan = $this->authorizedScan($request, $faceScan);

        // Compute all detailed metric items with overlays
        $items = $this->metrics->forScan($scan);

        // Return standardized metrics list wrapped in FaceMetricResource
        return response()->json([
            'message' => 'Face metrics.',
            'data'    => [
                'total'   => count($items),
                'metrics' => FaceMetricResource::collection($items),
            ],
        ]);
    }

    // Return a single metric detail modal payload with overlay coordinates
    public function show(Request $request, int $faceScan, string $key): JsonResponse
    {
        set_time_limit(180);

        // Fetch validated face scan record for the authorized user
        $scan = $this->authorizedScan($request, $faceScan);

        // Locate specific metric by key
        $metric = $this->metrics->findMetric($scan, $key);

        // Abort with 404 if metric key is invalid or not found
        abort_if(! $metric, 404, 'Metric not found.');

        // Return standardized single metric payload wrapped in FaceMetricResource
        return response()->json([
            'message' => 'Face metric.',
            'data'    => new FaceMetricResource($metric),
        ]);
    }

    // Authorize authenticated user and ensure scan is processed
    protected function authorizedScan(Request $request, int $faceScan)
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);
        abort_unless($scan->status === 'processed', 422, 'Scan is not ready yet.');

        return $scan;
    }
}
