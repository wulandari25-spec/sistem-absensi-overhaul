<?php
// tests/Unit/GeofencingServiceTest.php

namespace Tests\Unit;

use App\Services\GeofencingService;
use PHPUnit\Framework\TestCase;

class GeofencingServiceTest extends TestCase
{
    public function test_titik_yang_sama_menghasilkan_jarak_nol(): void
    {
        $jarak = GeofencingService::hitungJarakMeter(-7.7089, 113.5303, -7.7089, 113.5303);
        $this->assertEquals(0, round($jarak));
    }

    public function test_titik_di_luar_radius_terdeteksi_tidak_valid(): void
    {
        // Titik acuan Unit + titik ~500 meter (perbedaan ~0.0045 derajat lintang)
        $hasil = GeofencingService::dalamRadius(-7.7130, 113.5303, -7.7089, 113.5303, 100);
        $this->assertFalse($hasil['valid']);
    }

    public function test_titik_di_dalam_radius_terdeteksi_valid(): void
    {
        $hasil = GeofencingService::dalamRadius(-7.70895, 113.53035, -7.7089, 113.5303, 100);
        $this->assertTrue($hasil['valid']);
    }
}