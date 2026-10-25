<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicPage extends Model
{
    use HasFactory, SoftDeletes;

    // Table name
    protected $table = 'dynamic_pages';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'           => 'integer',
            'page_title'   => 'string',
            'page_slug'    => 'string',
            'page_content' => 'string',
            'status'       => 'string',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime',
            'deleted_at'   => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
}
