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

    protected $fillable = [
        'user_id',
        'front_image_path',
        'side_image_path',
        'harmony_score',
        'symmetry',
        'jawline',
        'cheekbones',
        'nose',
        'skin_quality',
        'angularity',
        'dimorphism',
        'facial_width_height_ratio',
        'key_strengths',
        'regions',
        'front_regions',
        'side_regions',
        'metrics',
        'summary',
        'report_pdf_path',
        'status',
        'report_pdf_path',
        'status',
        'overall_score',
        'score_version',
    ];

    protected $casts = [
        'key_strengths'             => 'array',
        'regions'                   => 'array',
        'harmony_score'             => 'float',
        'symmetry'                  => 'float',
        'jawline'                   => 'float',
        'cheekbones'                => 'float',
        'nose'                      => 'float',
        'skin_quality'              => 'float',
        'angularity'                => 'float',
        'dimorphism'                => 'float',
        'facial_width_height_ratio' => 'float',
        'front_regions'             => 'array',
        'side_regions'              => 'array',
        'metrics'                   => 'array',
        'overall_score' => 'float',
    ];

    protected $appends = ['report_pdf_url'];

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
