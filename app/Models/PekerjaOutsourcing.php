<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PekerjaOutsourcing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vendor_id', 'unit_instalasi_id',
        'nik', 'nama', 'foto_referensi', 'face_descriptor', 'status',
    ];

    protected $casts = [
        'face_descriptor' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function unitInstalasi()
    {
        return $this->belongsTo(UnitInstalasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logKeamanan()
    {
        return $this->hasMany(LogKeamanan::class);
    }

    public function qrTokens()
    {
        return $this->hasMany(QrToken::class);
    }

    /** Status kehadiran saat ini: apakah sedang di dalam unit atau sudah keluar */
    public function statusTerakhir()
    {
        return $this->logKeamanan()->latest('waktu_akses')->first()?->tipe_akses;
    }
}