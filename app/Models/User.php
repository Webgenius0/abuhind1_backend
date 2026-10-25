<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Table name
    protected $table = 'users';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'role',
        'provider',
        'provider_id',
        'provider_token',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['onboarding_completed'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'                 => 'integer',
            'user_id'            => 'string',
            'name'               => 'string',
            'email'              => 'string',
            'avatar'             => 'string',
            'agree_to_terms'     => 'boolean',
            'is_18_plus'         => 'boolean',
            'gender'             => 'string',
            'background'         => 'string',
            'provider'           => 'string',
            'provider_id'        => 'string',
            'provider_token'     => 'string',
            'role'               => 'string',
            'is_premium'         => 'boolean',
            'premium_expires_at' => 'datetime',
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'created_at'         => 'datetime',
            'updated_at'         => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
    public function faceScans(): HasMany
    {
        return $this->hasMany(FaceScan::class);
    }

    public function premiumCodes(): HasMany
    {
        return $this->hasMany(PremiumCode::class, 'assigned_user_id');
    }

    public function otps(): HasMany
    {
        return $this->hasMany(EmailOtp::class);
    }

    public function getOnboardingCompletedAttribute(): bool
    {
        return $this->is_18_plus
            && ! is_null($this->gender)
            && ! is_null($this->background);
    }

    public function isPremium(): bool
    {
        if (! $this->is_premium) {
            return false;
        }

        return $this->premium_expires_at === null || $this->premium_expires_at->isFuture();
    }
}
