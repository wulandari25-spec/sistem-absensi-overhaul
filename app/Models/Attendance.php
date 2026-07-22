<?php

namespace App\Models;

use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'verified_by', 'geofence_zone_id', 'shift_id', 'method', 'status',
        'latitude', 'longitude', 'proof_photo', 'confidence_score',
        'notes', 'is_flagged', 'flag_reason', 'device_info', 'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'method' => AttendanceMethod::class,
            'status' => AttendanceStatus::class,
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'confidence_score' => 'float',
            'is_flagged' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(OutsourcingStaff::class, 'staff_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function geofenceZone(): BelongsTo
    {
        return $this->belongsTo(GeofenceZone::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeCheckIns($query)
    {
        return $query->where('status', AttendanceStatus::CHECK_IN);
    }

    public function scopeCheckOuts($query)
    {
        return $query->where('status', AttendanceStatus::CHECK_OUT);
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('checked_at', today());
    }

    public function scopeRecent($query, int $limit = 50)
    {
        return $query->orderBy('checked_at', 'desc')->limit($limit);
    }
}
