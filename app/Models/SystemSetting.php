<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends Model
{
    use HasFactory, SoftDeletes;

    // Table name
    protected $table = 'system_settings';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'                  => 'integer',
            'title'               => 'string',
            'email'               => 'string',
            'system_name'         => 'string',
            'phone'               => 'string',
            'address'             => 'string',
            'timezone'            => 'string',
            'maintenance_mode'    => 'boolean',
            'maintenance_message' => 'string',
            'copyright_text'      => 'string',
            'logo'                => 'string',
            'favicon'             => 'string',
            'description'         => 'string',
            'created_at'          => 'datetime',
            'updated_at'          => 'datetime',
            'deleted_at'          => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
}
