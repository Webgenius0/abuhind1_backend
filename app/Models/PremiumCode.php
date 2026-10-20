<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumCode extends Model
{
    protected $fillable = [
        'code', 'validity_days', 'code_expires_at', 'assigned_user_id',
        'used_by', 'used_at', 'created_by', 'note',
    ];

    protected $casts = [
        'validity_days'   => 'integer',
        'code_expires_at' => 'datetime',
        'used_at'         => 'datetime',
    ];

    protected $appends = ['status'];

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

    public function scopeWithStatus($query, ?string $status)
    {
        return match ($status) {
            'used'    => $query->whereNotNull('used_at'),
            'expired' => $query->whereNull('used_at')
                ->whereNotNull('code_expires_at')
                ->where('code_expires_at', '<=', now()),
            'unused'  => $query->whereNull('used_at')
                ->where(fn ($q) => $q->whereNull('code_expires_at')->orWhere('code_expires_at', '>', now())),
            default   => $query,
        };
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
