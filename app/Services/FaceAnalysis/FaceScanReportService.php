<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FaceScanReportService
{
    /**
     * Render the full overview PDF for a scan and return its storage path.
     */
    public function generate(FaceScan $scan): string
    {
        $pdf = Pdf::loadView('pdf.face-scan-overview', compact('scan'));

        $path = "face-scans/reports/{$scan->id}-overview.pdf";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
