<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutsourcingStaff extends Model
{
    use HasFactory;

    protected $table = 'outsourcing_staffs';

    protected $fillable = [
        'staff_code', 'name', 'institution', 'department', 'position',
        'face_descriptor', 'photo_profile', 'phone', 'id_number',
        'is_active_onsite', 'last_seen_at', 'is_registered', 'password',
        'contract_start_date', 'contract_end_date',
    ];

    protected function casts(): array
    {
        return [
            'face_descriptor' => 'array',
            'is_active_onsite' => 'boolean',
            'is_registered' => 'boolean',
            'last_seen_at' => 'datetime',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
        ];
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'staff_id');
    }

    public function qrTokens(): HasMany
    {
        return $this->hasMany(QrToken::class, 'staff_id');
    }

    public function scopeActiveOnsite($query)
    {
        return $query->where('is_active_onsite', true);
    }

    public function scopeRegistered($query)
    {
        return $query->where('is_registered', true);
    }

    public function scopeWithFaceData($query)
    {
        return $query->whereNotNull('face_descriptor');
    }

    public function latestCheckIn()
    {
        return $this->attendances()
            ->where('status', 'check_in')
            ->latest('checked_at')
            ->first();
    }

    public function markOnsite(): void
    {
        $this->update([
            'is_active_onsite' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function markOffsite(): void
    {
        $this->update([
            'is_active_onsite' => false,
            'last_seen_at' => now(),
        ]);
    }

    public function isWithinContract($date): bool
    {
        if (!$this->contract_start_date || !$this->contract_end_date) {
            return true;
        }
        $checkDate = \Carbon\Carbon::parse($date)->startOfDay();
        return $checkDate->between(
            $this->contract_start_date->startOfDay(),
            $this->contract_end_date->endOfDay()
        );
    }
}
