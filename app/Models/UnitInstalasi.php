<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitInstalasi extends Model
{
    use HasFactory;

    protected $fillable = ['nama_unit', 'latitude', 'longitude', 'radius_meter'];

    public function pekerja()
    {
        return $this->hasMany(PekerjaOutsourcing::class);
    }
}