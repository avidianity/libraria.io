<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAPITest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login with correct credentials.
     *
     * @return void
     */
    public function testLoginWithCorrectCredentials()
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'admin@admin.com'
        ]);

        $response = $this->post(
            '/api/v1/auth/login',
            [
                'email' => 'admin@admin.com',
                'password' => 'password',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertOk();
    }

    /**
     * Test login with incorrect credentials.
     *
     * @return void
     */
    public function testLoginWithIncorrectCredentials()
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'admin@admin.com'
        ]);

        $response = $this->post('/api/v1/auth/login', [
            'email' => 'admin@admin.com',
            'password' => 'passwordd',
        ], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    /** 
     * Test login with missing credentials.
     *
     * @return void
     */
    public function testLoginWithMissingCredentials()
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'admin@admin.com'
        ]);

        $response = $this->post(
            '/api/v1/auth/login',
            [],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);
    }
}
