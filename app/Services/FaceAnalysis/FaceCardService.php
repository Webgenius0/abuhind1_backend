<?php

namespace App\Services\FaceAnalysis;

use App\Models\FaceScan;
use App\Models\FaceScanCard;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

class FaceCardService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        // Swap Driver::class for \Intervention\Image\Drivers\Imagick\Driver
        // if the server has the Imagick extension instead of GD.
        $this->imageManager = new ImageManager(new Driver());
    }

    public function generate(FaceScan $scan): FaceScanCard
    {
        $card = $scan->card ?: new FaceScanCard(['face_scan_id' => $scan->id]);

        $harmony    = $scan->harmony_score;
        $features   = round((($scan->jawline + $scan->cheekbones + $scan->nose) / 3), 1);
        $angularity = $scan->angularity;
        $dimorphism = $scan->dimorphism;

        $card->fill([
            'harmony'    => $harmony,
            'features'   => $features,
            'angularity' => $angularity,
            'dimorphism' => $dimorphism,
            'overall'    => round(($harmony + $features + $angularity + $dimorphism) / 4, 1),
        ])->save();

        $card->image_path = $this->renderImage($scan, $card);
        $card->pdf_path    = $this->renderPdf($scan, $card);
        $card->save();

        return $card;
    }

    protected function renderImage(FaceScan $scan, FaceScanCard $card): string
    {
        $canvas = $this->imageManager->create(600, 800)->fill('ffffff');

        $photo = $this->imageManager
            ->read(Storage::disk('public')->path($scan->front_image_path))
            ->cover(560, 400);

        $canvas->place($photo, 'top', 20, 20);

        $canvas->text((string) $card->overall, 300, 480, function ($font) {
            $font->size(56);
            $font->align('center');
        });

        $lines = [
            "Harmony: {$card->harmony}",
            "Features: {$card->features}",
            "Angularity: {$card->angularity}",
            "Dimorphism: {$card->dimorphism}",
        ];

        foreach ($lines as $i => $line) {
            $canvas->text($line, 60, 580 + ($i * 40), function ($font) {
                $font->size(22);
                $font->align('left');
            });
        }

        $path = "face-scans/cards/{$scan->id}-card.png";
        Storage::disk('public')->put($path, (string) $canvas->encode(new PngEncoder()));

        return $path;
    }

    protected function renderPdf(FaceScan $scan, FaceScanCard $card): string
    {
        $pdf = Pdf::loadView('pdf.face-scan-card', compact('scan', 'card'));

        $path = "face-scans/cards/{$scan->id}-card.pdf";
        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
