<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['nama_vendor', 'kontak', 'alamat'];

    public function pekerja()
    {
        return $this->hasMany(PekerjaOutsourcing::class);
    }
}