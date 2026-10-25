<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTutorial extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'video_tutorials';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'title'        => 'string',
            'description'  => 'string',
            'youtube_link' => 'string',
            'status'       => 'string',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
}
