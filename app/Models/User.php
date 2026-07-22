<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPermissions;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function verifiedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::ADMIN, UserRole::SUPERADMIN]);
    }

    public function isK3(): bool
    {
        return $this->role === UserRole::K3;
    }

    public function isSecurity(): bool
    {
        return $this->role === UserRole::SECURITY;
    }

    /**
     * Superadmin otomatis punya semua permission Spatie,
     * tanpa perlu di-assign manual satu per satu.
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->role === UserRole::SUPERADMIN) {
            return true;
        }

        return parent::hasPermissionTo($permission, $guardName);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}