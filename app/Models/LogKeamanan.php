<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogKeamanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pekerja_outsourcing_id', 'tipe_akses', 'metode',
        'latitude', 'longitude', 'jarak_meter', 'status_validasi', 'waktu_akses',
    ];

    protected $casts = [
        'waktu_akses' => 'datetime',
    ];

    public function pekerja()
    {
        return $this->belongsTo(PekerjaOutsourcing::class, 'pekerja_outsourcing_id');
    }
}