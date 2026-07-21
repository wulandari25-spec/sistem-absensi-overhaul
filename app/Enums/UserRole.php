<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case SECURITY = 'security';
    case SUPERADMIN = 'superadmin';
    case K3 = 'k3';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::SECURITY => 'Petugas Keamanan',
            self::SUPERADMIN => 'Super Administrator',
            self::K3 => 'Safety Officer (K3)',
        };
    }
}
