<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'agree_to_terms',
        'is_18_plus',
        'gender',
        'background',
        'password',
        'provider',
        'provider_id',
        'provider_token',
        'role',
        'is_premium',
        'premium_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'role',
        'provider',
        'provider_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_premium' => 'boolean',
            'premium_expires_at' => 'datetime',
        ];
    }
    public function isPremium(): bool
    {
        if (! $this->is_premium) {
            return false;
        }

        return $this->premium_expires_at === null || $this->premium_expires_at->isFuture();
    }
}
