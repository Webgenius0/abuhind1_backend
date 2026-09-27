<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FaceScanCard extends Model
{
    protected $fillable = [
        'face_scan_id',
        'harmony',
        'features',
        'angularity',
        'dimorphism',
        'overall',
        'image_path',
        'pdf_path',
    ];

    protected $casts = [
        'harmony'    => 'float',
        'features'   => 'float',
        'angularity' => 'float',
        'dimorphism' => 'float',
        'overall'    => 'float',
    ];

    protected $appends = ['image_url', 'pdf_url'];

    public function faceScan(): BelongsTo
    {
        return $this->belongsTo(FaceScan::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path ? Storage::disk('public')->url($this->pdf_path) : null;
    }
}
