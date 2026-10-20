<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class FaceScanReportService
{
    public function generate(FaceScan $scan): string
    {
        $html = view('pdf.face-scan-overview', compact('scan'))->render();

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'directionality'   => 'rtl',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'default_font'     => 'dejavusans',
            'tempDir'          => $tempDir,
        ]);

        $mpdf->WriteHTML($html);

        $path = "face-scans/reports/{$scan->id}-overview.pdf";

        Storage::disk('public')->put($path, $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN));

        return $path;
    }
}
