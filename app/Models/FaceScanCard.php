<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FaceScanCard extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'face_scan_cards';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [];

    protected $appends = ['image_url', 'pdf_url'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'face_scan_id' => 'integer',
            'harmony'      => 'float',
            'features'     => 'float',
            'angularity'   => 'float',
            'dimorphism'   => 'float',
            'overall'      => 'float',
            'image_path'   => 'string',
            'pdf_path'     => 'string',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
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
