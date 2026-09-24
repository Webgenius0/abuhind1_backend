<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FaceAnalysis\FaceAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FaceAnalysisController extends Controller
{
    public function __construct(
        protected FaceAnalysisService $faceAnalysisService
    ) {
    }

    /**
     * Handle the incoming face scan request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Request Validation
        $request->validate([
            'front' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:10240'], // max 10MB
            'side'  => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:10240'],
        ]);

        // 2. Authenticated user id (jodi authentication thake)
        $userId = $request->user()->id;

        // 3. Service call kore analysis kora
        $faceScan = $this->faceAnalysisService->analyze(
            userId: $userId,
            front: $request->file('front'),
            side: $request->file('side')
        );

        // 4. Response return kora
        return response()->json([
            'message' => 'Face analysis completed successfully.',
            'data'    => $faceScan,
        ], 201);
    }
}
