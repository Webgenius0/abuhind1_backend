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
        protected FaceScanReportService $reportService,
    ) {
    }

    // generateCard() intentionally lives in FaceAnalysisController, not here —
    // it needs Request/auth context, which the service layer shouldn't touch.

    public function analyze(int $userId, UploadedFile $front, ?UploadedFile $side = null, bool $detailed = false): FaceScan
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

            $analysis = $this->gemini->analyzeFace($images, $detailed);

            $scan = $this->faceScans->update($scan, [
                'harmony_score'             => $analysis['harmony_score'],
                'symmetry'                  => $analysis['symmetry'],
                'jawline'                   => $analysis['jawline'],
                'cheekbones'                => $analysis['cheekbones'],
                'nose'                      => $analysis['nose'],
                'skin_quality'              => $analysis['skin_quality'],
                'angularity'                => $analysis['angularity'],
                'dimorphism'                => $analysis['dimorphism'],
                'facial_width_height_ratio' => $analysis['facial_width_height_ratio'] ?? null,
                'key_strengths'             => $analysis['key_strengths'],
                'regions'                   => $analysis['regions'] ?? [],
                'front_regions'             => $analysis['front_regions'] ?? null,
                'side_regions'              => $analysis['side_regions'] ?? null,
                'summary'                   => $analysis['summary'],
                'status'                    => 'processed',
            ]);

            $reportPath = $this->reportService->generate($scan);

            return $this->faceScans->update($scan, ['report_pdf_path' => $reportPath]);
        } catch (Throwable $e) {
            $this->faceScans->update($scan, ['status' => 'failed']);

            throw $e;
        }
    }
}
