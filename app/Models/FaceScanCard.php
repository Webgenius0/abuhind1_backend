<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceScanCard extends Model
{
    protected $fillable = [
        'face_scan_id',
        'harmony',
        'features',
        'originality',
        'description',
        'image_path',
        'pdf_path',
    ];

    protected $casts = [
        'harmony'     => 'float',
        'features'    => 'float',
        'originality' => 'float',
        'description' => 'float',
    ];

    public function faceScan(): BelongsTo
    {
        return $this->belongsTo(FaceScan::class);
    }
}
