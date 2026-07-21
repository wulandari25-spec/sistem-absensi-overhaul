<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QrToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'token', 'purpose', 'is_used', 'expires_at', 'used_at',
    ];

    protected function casts(): array
    {
        return [
            'purpose' => AttendanceStatus::class,
            'is_used' => 'boolean',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(OutsourcingStaff::class, 'staff_id');
    }

    public function scopeValid($query)
    {
        return $query->where('is_used', false)->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }

    public function markUsed(): void
    {
        $this->update(['is_used' => true, 'used_at' => now()]);
    }
}
