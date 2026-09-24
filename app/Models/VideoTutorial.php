<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoTutorial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'youtube_link',
        'status',
    ];
}
