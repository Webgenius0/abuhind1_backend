<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailOtp extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'email_otps';

    public $timestamps = false;

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = ['verification_code', 'password'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'                => 'integer',
            'user_id'           => 'integer',
            'verification_code' => 'string',
            'expires_at'        => 'datetime',
            'name'              => 'string',
            'email'             => 'string',
            'phone_code'        => 'string',
            'phone'             => 'string',
            'avatar'            => 'string',
            'password'          => 'string',
        ];
    }

    // Relationships and other model methods can be added here
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
