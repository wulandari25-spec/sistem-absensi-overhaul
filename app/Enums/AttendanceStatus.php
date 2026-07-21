<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case CHECK_IN = 'check_in';
    case CHECK_OUT = 'check_out';
    case PERMIT = 'permit';
    case SICK = 'sick';

    public function label(): string
    {
        return match($this) {
            self::CHECK_IN => 'Masuk',
            self::CHECK_OUT => 'Keluar',
            self::PERMIT => 'Izin',
            self::SICK => 'Sakit',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CHECK_IN => 'emerald',
            self::CHECK_OUT => 'rose',
            self::PERMIT => 'amber',
            self::SICK => 'rose', // Or red
        };
    }
}
