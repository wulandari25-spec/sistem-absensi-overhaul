<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not loaded.');
        }

        parent::setUp();

        Storage::fake('local');
    }

    public function test_can_create_staff_with_base64_captured_photo()
    {
        // 1. Create admin user
        $user = \App\Models\User::factory()->create();

        // 2. Prepare request data with base64 image data
        $base64Image = 'data:image/jpeg;base64,' . base64_encode('fake image content');
        
        $data = [
            'staff_code' => 'OS-9999',
            'name' => 'John Doe Camera',
            'institution' => 'Test Institution',
            'department' => 'Unit Pelayanan',
            'position' => 'Security',
            'photo_profile_captured' => $base64Image,
            'contract_start_date' => now()->format('Y-m-d'),
            'contract_end_date' => now()->addDays(30)->format('Y-m-d'), // minimum 20 days
        ];

        // 3. Post to store route
        $response = $this->actingAs($user)->post(route('admin.staffs.store'), $data);

        // 4. Verify redirected (saved successfully)
        $response->assertRedirect(route('admin.staffs.index'));

        // 5. Verify database record exists
        $staff = \App\Models\OutsourcingStaff::where('staff_code', 'OS-9999')->first();
        $this->assertNotNull($staff);
        $this->assertNotNull($staff->photo_profile);
        $this->assertStringContainsString('staff-photos/', $staff->photo_profile);

        // 6. Verify file exists in local storage
        Storage::disk('local')->assertExists($staff->photo_profile);
        $this->assertEquals('fake image content', Storage::disk('local')->get($staff->photo_profile));
    }

    public function test_cannot_create_duplicate_staff_name_in_same_institution()
    {
        $user = \App\Models\User::factory()->create();

        // Create first staff
        \App\Models\OutsourcingStaff::create([
            'staff_code' => 'OS-1001',
            'name' => 'Desiyatul Husna',
            'institution' => 'PT. Daya Energi Mandiri',
            'is_registered' => true,
        ]);

        // Try to create second staff with same name and institution
        $data = [
            'staff_code' => 'OS-1002',
            'name' => 'Desiyatul Husna',
            'institution' => 'PT. Daya Energi Mandiri',
            'contract_start_date' => now()->format('Y-m-d'),
            'contract_end_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post(route('admin.staffs.store'), $data);

        // Assert session has errors for name
        $response->assertSessionHasErrors('name');
    }

    public function test_cannot_create_duplicate_staff_id_number()
    {
        $user = \App\Models\User::factory()->create();

        // Create first staff with a NIK
        \App\Models\OutsourcingStaff::create([
            'staff_code' => 'OS-2001',
            'name' => 'John Doe',
            'institution' => 'PT. Daya Energi Mandiri',
            'id_number' => '1234567890123456',
            'is_registered' => true,
        ]);

        // Try to create second staff with same NIK
        $data = [
            'staff_code' => 'OS-2002',
            'name' => 'Jane Doe',
            'institution' => 'PT. Daya Energi Mandiri',
            'id_number' => '1234567890123456',
            'contract_start_date' => now()->format('Y-m-d'),
            'contract_end_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post(route('admin.staffs.store'), $data);

        // Assert session has errors for id_number
        $response->assertSessionHasErrors('id_number');
    }
}
