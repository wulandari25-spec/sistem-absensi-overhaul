<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageServeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fake disks for testing
        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_serves_file_from_local_disk()
    {
        // 1. Put file in local disk
        Storage::disk('local')->put('test-folder/test-file.txt', 'hello local storage');

        // 2. Request the file
        $response = $this->get('/storage/test-folder/test-file.txt');

        // 3. Verify response
        $response->assertStatus(200);
        
        // Read stream content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        $this->assertEquals('hello local storage', $content);
    }

    public function test_serves_file_from_public_disk_fallback()
    {
        // 1. Put file in public disk
        Storage::disk('public')->put('test-folder/test-public-file.txt', 'hello public storage');

        // 2. Request the file
        $response = $this->get('/storage/test-folder/test-public-file.txt');

        // 3. Verify response
        $response->assertStatus(200);
        
        // Read stream content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        
        $this->assertEquals('hello public storage', $content);
    }

    public function test_returns_404_for_non_existent_file()
    {
        // Request a file that does not exist on either disk
        $response = $this->get('/storage/non-existent.txt');

        // Verify response
        $response->assertStatus(404);
    }
}
