<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\Gemini\GeminiVisionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FaceAnalysisService
{
    public function __construct(
        protected GeminiVisionService $gemini,
        protected FaceScanRepositoryInterface $faceScans,
    ) {
    }

    public function analyze(int $userId, UploadedFile $front, ?UploadedFile $side = null): FaceScan
    {
        $frontPath = $front->store('face-scans', 'public');
        $sidePath  = $side?->store('face-scans', 'public');

        $scan = $this->faceScans->create([
            'user_id'          => $userId,
            'front_image_path' => $frontPath,
            'side_image_path'  => $sidePath,
            'status'           => 'pending',
        ]);

        try {
            $images = ['front' => Storage::disk('public')->path($frontPath)];

            if ($sidePath) {
                $images['side'] = Storage::disk('public')->path($sidePath);
            }

            $analysis = $this->gemini->analyzeFace($images);

            return $this->faceScans->update($scan, [
                'harmony_score'             => $analysis['harmony_score'],
                'symmetry'                  => $analysis['symmetry'],
                'jawline'                   => $analysis['jawline'],
                'cheekbones'                => $analysis['cheekbones'],
                'nose'                      => $analysis['nose'],
                'skin_quality'              => $analysis['skin_quality'],
                'facial_width_height_ratio' => $analysis['facial_width_height_ratio'] ?? null,
                'key_strengths'             => $analysis['key_strengths'],
                'regions'                   => $analysis['regions'] ?? [],
                'summary'                   => $analysis['summary'],
                'status'                    => 'processed',
            ]);
        } catch (Throwable $e) {
            $this->faceScans->update($scan, ['status' => 'failed']);

            throw $e;
        }
    }
}
