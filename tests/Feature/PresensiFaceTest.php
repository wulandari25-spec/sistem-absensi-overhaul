<?php

namespace Tests\Feature;

use Tests\TestCase;

class PresensiFaceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        // The root path redirects to attendance.check-in, which redirects to employee.login
        $response->assertStatus(302);
    }
}
