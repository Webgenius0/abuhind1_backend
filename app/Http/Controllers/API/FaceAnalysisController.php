<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\FaceAnalysis\FaceAnalysisService;
use App\Services\FaceAnalysis\FaceCardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\FaceScanResource;
use App\Http\Resources\FaceScanRegionsResource;
use App\Services\FaceAnalysis\ScorePercentileService;
use App\Traits\ApiResponse;

class FaceAnalysisController extends Controller
{
    use ApiResponse;
    public function __construct(
        protected FaceAnalysisService $faceAnalysisService,
        protected FaceScanRepositoryInterface $faceScans,
        protected FaceCardService $cardService,
    ) {
    }

    /**
     * Handle the incoming face scan request.
     *
     * @throws ValidationException
     */

    public function myScans(Request $request): JsonResponse
    {
        $scans = $this->faceScans->forUser($request->user()->id);

        return $this->success('Your scans.', [
            'total_scans' => $scans->count(),
            'scans'       => FaceScanResource::collection($scans),
        ], 200);
    }
    public function store(Request $request): JsonResponse
    {
        set_time_limit(180);
        // 1. Request Validation
        $request->validate([
            'front' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // max 10MB
            'side'  => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        // 2. Authenticated user id (jodi authentication thake)
        $userId = $request->user()->id;

        // 3. Service call kore analysis kora
        // $faceScan = $this->faceAnalysisService->analyze(
        //     userId: $userId,
        //     front: $request->file('front'),
        //     side: $request->file('side')
        // );
        $user = $request->user();

        if (! $user->isPremium() && $this->faceScans->countForUser($user->id) >= 1) {
            return $this->error('Free users can only perform 1 scan. Upgrade to Premium for unlimited scans.', [], 403);
        }

        $faceScan = $this->faceAnalysisService->analyze(
            userId: $userId,
            front: $request->file('front'),
            side: $request->file('side'),
            detailed: true,
        );
        // 4. Response return kora
        return response()->json([
            'message' => 'Face analysis completed successfully.',
            'data'    => $faceScan,
        ], 201);
    }
    public function generateCard(Request $request, int $faceScan): JsonResponse
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);
        abort_unless($scan->status === 'processed', 422, 'Scan is not ready yet.');

        $card = $this->cardService->generate($scan);

        return response()->json([
            'message' => 'Card generated.',
            'data'    => $card,
        ], 201);
    }
    public function show(Request $request, int $faceScan): JsonResponse
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);

        return response()->json([
            'message' => 'Face scan details.',
            'data'    => new FaceScanResource($scan),
        ]);
    }
    public function analyze(Request $request, int $faceScan): JsonResponse
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);

        return response()->json([
            'message' => 'Face region analysis.',
            'data'    => new FaceScanRegionsResource($scan),
        ]);
    }
    public function comparison(Request $request, int $faceScan, ScorePercentileService $percentiles)
    {
        $scan = $this->faceScans->findOrFail($faceScan);

        abort_unless($scan->user_id === $request->user()->id, 403);
        abort_unless($scan->status === 'processed', 422, 'Scan is not ready yet.');

        return $this->success('Score comparison.', $percentiles->for($scan), 200);
    }
}
