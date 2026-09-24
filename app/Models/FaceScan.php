<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'facial_width_height_ratio',
        'key_strengths',
        'regions',
        'summary',
        'status',
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
        'facial_width_height_ratio' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function card(): HasOne
    {
        return $this->hasOne(FaceScanCard::class);
    }
}
