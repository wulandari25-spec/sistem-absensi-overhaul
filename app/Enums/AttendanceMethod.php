<?php

namespace App\Enums;

enum AttendanceMethod: string
{
    case FACE_RECOGNITION = 'face_recognition';
    case QR_CODE = 'qr_code';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match($this) {
            self::FACE_RECOGNITION => 'Face Recognition',
            self::QR_CODE => 'QR Code',
            self::MANUAL => 'Manual (Izin/Sakit)',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::FACE_RECOGNITION => '👤',
            self::QR_CODE => '📱',
            self::MANUAL => '📝',
        };
    }
}
