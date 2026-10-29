<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicGallery extends Model
{
    use HasFactory;

    protected $table = 'dynamic_galleries';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'id'          => 'integer',
            'type'        => 'string',
            'title'       => 'string',
            'subtitle'    => 'string',
            'description' => 'string',
            'right_label' => 'string',
            'wrong_label' => 'string',
            'status'      => 'string',
            'created_at'  => 'datetime',
            'updated_at'  => 'datetime',
        ];
    }

    // Accessor for the right_image attribute
    public function getRightImageAttribute(?string $url): ?string
    {
        if ($url) {
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                return $url;
            } else {
                return asset('/' . ltrim($url, '/'));
            }
        }

        return null;
    }

    // Accessor for the wrong_image attribute
    public function getWrongImageAttribute(?string $url): ?string
    {
        if ($url) {
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                return $url;
            } else {
                return asset('/' . ltrim($url, '/'));
            }
        }

        return null;
    }
}
