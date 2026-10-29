<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumCode extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'premium_codes';

    // The attributes that are mass assignable.
    protected $guarded = [];

    // The attributes that should be hidden for serialization.
    protected $hidden = [];

    protected $appends = ['status'];

    // The attributes that should be cast.
    protected function casts(): array
    {
        return [
            'id'               => 'integer',
            'code'             => 'string',
            'validity_days'    => 'integer',
            'code_expires_at'  => 'datetime',
            'assigned_user_id' => 'integer',
            'used_by'          => 'integer',
            'used_at'          => 'datetime',
            'created_by'       => 'integer',
            'note'             => 'string',
            'created_at'       => 'datetime',
            'updated_at'       => 'datetime',
        ];
    }

    // Relationships and other model methods can be added here
    public function getStatusAttribute(): string
    {
        if ($this->used_at) {
            return 'used';
        }

        if ($this->code_expires_at && $this->code_expires_at->isPast()) {
            return 'expired';
        }

        return 'unused';
    }

    // scope to filter by status
    public function scopeWithStatus($query, ?string $status)
    {
        return match ($status) {
            'used'    => $query->whereNotNull('used_at'),
            'expired' => $query->whereNull('used_at')
                ->whereNotNull('code_expires_at')
                ->where('code_expires_at', '<=', now()),
            'unused'  => $query->whereNull('used_at')
                ->where(fn($q) => $q->whereNull('code_expires_at')->orWhere('code_expires_at', '>', now())),
            default   => $query,
        };
    }

    // assign to user
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
