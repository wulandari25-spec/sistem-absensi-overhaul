<?php
 
namespace Tests\Feature;
 
use App\Models\User;
use App\Models\MasterInstitution;
use App\Models\MasterDepartment;
use App\Models\MasterPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
 
class MasterDataTest extends TestCase
{
    use RefreshDatabase;
 
    protected function setUp(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO SQLite extension is not loaded.');
        }
        parent::setUp();
    }
 
    public function test_admin_can_access_master_data_index()
    {
        $user = User::factory()->create(['role' => 'admin']);
 
        $response = $this->actingAs($user)->get(route('admin.master-data.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.master_data.index');
    }
 
    public function test_admin_can_create_master_institution()
    {
        $user = User::factory()->create(['role' => 'admin']);
 
        $response = $this->actingAs($user)->post(route('admin.master-data.institutions.store'), [
            'name' => 'PT. Test Mandiri',
        ]);
 
        $response->assertRedirect(route('admin.master-data.index'));
        $this->assertDatabaseHas('master_institutions', ['name' => 'PT. Test Mandiri']);
    }
 
    public function test_admin_can_update_master_institution()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $institution = MasterInstitution::create(['name' => 'PT. Old Mandiri']);
 
        $response = $this->actingAs($user)->put(route('admin.master-data.institutions.update', $institution->id), [
            'name' => 'PT. New Mandiri',
        ]);
 
        $response->assertRedirect(route('admin.master-data.index'));
        $this->assertDatabaseHas('master_institutions', ['name' => 'PT. New Mandiri']);
        $this->assertDatabaseMissing('master_institutions', ['name' => 'PT. Old Mandiri']);
    }
 
    public function test_admin_can_delete_master_institution()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $institution = MasterInstitution::create(['name' => 'PT. Deleted Mandiri']);
 
        $response = $this->actingAs($user)->delete(route('admin.master-data.institutions.destroy', $institution->id));
 
        $response->assertRedirect(route('admin.master-data.index'));
        $this->assertDatabaseMissing('master_institutions', ['name' => 'PT. Deleted Mandiri']);
    }
 
    public function test_k3_user_cannot_modify_master_institution()
    {
        // K3 role is simulated by setting role appropriately in user factory
        $user = User::factory()->create(['role' => 'k3']);
 
        $response = $this->actingAs($user)->post(route('admin.master-data.institutions.store'), [
            'name' => 'PT. Illegal Mandiri',
        ]);
 
        $response->assertStatus(403);
        $this->assertDatabaseMissing('master_institutions', ['name' => 'PT. Illegal Mandiri']);
    }
}
