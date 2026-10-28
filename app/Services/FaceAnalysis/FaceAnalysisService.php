<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use App\Repositories\Contracts\FaceScanRepositoryInterface;
use App\Services\Gemini\GeminiVisionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class FaceAnalysisService
{
    public function __construct(
        protected GeminiVisionService $gemini,
        protected FaceScanRepositoryInterface $faceScans,
        protected FaceScanReportService $reportService,
    ) {}

    // generateCard() intentionally lives in FaceAnalysisController, not here —
    // it needs Request/auth context, which the service layer shouldn't touch.

    public function analyze(int $userId, UploadedFile $front, ?UploadedFile $side = null, bool $detailed = false): FaceScan
    {
        // 1. Instant pre-check: verify images are not completely black, blank, or uniform
        if ($this->isInvalidOrBlankImage($front->getRealPath())) {
            throw ValidationException::withMessages([
                'front' => ['Front photo is too dark. Please upload a well-lit photo.'],
            ]);
        }

        if ($side && $this->isInvalidOrBlankImage($side->getRealPath())) {
            throw ValidationException::withMessages([
                'side' => ['Side photo is too dark. Please upload a well-lit photo.'],
            ]);
        }

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

            // 2. AI Face Validation check
            if (isset($analysis['face_detected']) && ! $analysis['face_detected']) {
                $isSideFail = ($sidePath && isset($analysis['side_face_detected']) && ! $analysis['side_face_detected']);
                $failedField = $isSideFail ? 'side' : 'front';
                $errorMessage = $isSideFail
                    ? 'Face not detected in side view. Please upload a clear photo.'
                    : 'Face not detected in front view. Please upload a clear photo.';

                throw ValidationException::withMessages([
                    $failedField => [$errorMessage],
                ]);
            }

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
            $scan->update([
                'overall_score' => $scan->calculateOverall(),
                'score_version' => config('scoring.version'),
            ]);
            $reportPath = $this->reportService->generate($scan);

            return $this->faceScans->update($scan, ['report_pdf_path' => $reportPath]);
        } catch (ValidationException $e) {
            Storage::disk('public')->delete($frontPath);
            if ($sidePath) {
                Storage::disk('public')->delete($sidePath);
            }
            $scan->delete();

            throw $e;
        } catch (Throwable $e) {
            $this->faceScans->update($scan, ['status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Fast local pre-check to detect completely black, dark, blank, or flat single-color images.
     */
    protected function isInvalidOrBlankImage(string $path): bool
    {
        if (! file_exists($path)) {
            return true;
        }

        $info = @getimagesize($path);
        if (! $info || empty($info[0]) || empty($info[1])) {
            return true;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            default        => null,
        };

        if (! $image) {
            return false;
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Fast sample on a 16x16 thumbnail (< 1ms execution)
        $thumb = imagecreatetruecolor(16, 16);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, 16, 16, $width, $height);

        $minLum = 255.0;
        $maxLum = 0.0;
        $totalLum = 0.0;

        for ($x = 0; $x < 16; $x++) {
            for ($y = 0; $y < 16; $y++) {
                $rgb = imagecolorat($thumb, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $lum = 0.299 * $r + 0.587 * $g + 0.114 * $b;

                if ($lum < $minLum) {
                    $minLum = $lum;
                }
                if ($lum > $maxLum) {
                    $maxLum = $lum;
                }
                $totalLum += $lum;
            }
        }

        imagedestroy($thumb);
        imagedestroy($image);

        $avgLum = $totalLum / 256.0;

        // Pitch black or extremely dark image
        if ($maxLum < 15.0 || $avgLum < 10.0) {
            return true;
        }

        // Solid uniform color with no contrast (e.g. blank white, gray, red screen)
        if (($maxLum - $minLum) < 5.0) {
            return true;
        }

        return false;
    }
}
