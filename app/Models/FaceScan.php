<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class FaceScan extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'face_scans';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [];

    protected $appends = ['report_pdf_url'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'                        => 'integer',
            'user_id'                   => 'integer',
            'front_image_path'          => 'string',
            'side_image_path'           => 'string',
            'harmony_score'             => 'float',
            'symmetry'                  => 'float',
            'jawline'                   => 'float',
            'cheekbones'                => 'float',
            'nose'                      => 'float',
            'skin_quality'              => 'float',
            'angularity'                => 'float',
            'dimorphism'                => 'float',
            'facial_width_height_ratio' => 'float',
            'key_strengths'             => 'array',
            'regions'                   => 'array',
            'front_regions'             => 'array',
            'side_regions'              => 'array',
            'metrics'                   => 'array',
            'summary'                   => 'string',
            'report_pdf_path'           => 'string',
            'status'                    => 'string',
            'overall_score'             => 'float',
            'score_version'             => 'string',
            'created_at'                => 'datetime',
            'updated_at'                => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function card(): HasOne
    {
        return $this->hasOne(FaceScanCard::class);
    }

    public function getReportPdfUrlAttribute(): ?string
    {
        return $this->report_pdf_path ? Storage::disk('public')->url($this->report_pdf_path) : null;
    }

    public function calculateFeatures(): ?float
    {
        if ($this->jawline === null || $this->cheekbones === null || $this->nose === null) {
            return null;
        }

        return round(($this->jawline + $this->cheekbones + $this->nose) / 3, 1);
    }

    public function calculateOverall(): ?float
    {
        $features = $this->calculateFeatures();

        if ($this->harmony_score === null || $features === null
            || $this->angularity === null || $this->dimorphism === null) {
            return null;
        }

        return round(($this->harmony_score + $features + $this->angularity + $this->dimorphism) / 4, 1);
    }
}
