<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
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

    /**
     * Test register with correct credentials.
     * 
     * @return void
     */
    public function testRegisterWithCorrectCredentials()
    {
        Role::create(['name' => User::NORMAL]);

        $response = $this->post(
            '/api/v1/auth/register',
            [
                'name' => 'John Doe',
                'email' => 'johndoe@admin.com',
                'password' => 'password',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertCreated();
    }

    /**
     * Test register with missing credentials.
     * 
     * @return void
     */
    public function testRegisterWithMissingCredentials()
    {
        Role::create(['name' => User::NORMAL]);

        $response = $this->post(
            '/api/v1/auth/register',
            [
                'name' => 'John Doe',
                'email' => 'johndoe@admin.com',
            ],
            ['Accept' => 'application/json']
        );

        $response->assertStatus(422);
    }

    /**
     * Test register with correct credentials.
     * 
     * @return void
     */
    public function testRegisteringAnAdmin()
    {
        $role = Role::create(['name' => User::ADMIN]);
        $user = User::factory()->create([
            'email' => 'admin@admin.com'
        ]);

        $user->assignRole($role);
        $user->save();

        $token = $user->createToken('admin');

        $response = $this->post(
            '/api/v1/auth/register/admin',
            [
                'name' => 'John Doe',
                'email' => 'johndoe@admin.com',
                'password' => 'password',
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertCreated();
    }

    /**
     * Test register with correct credentials.
     * 
     * @return void
     */
    public function testRegisteringAnAdminAsUnauthorizedUser()
    {
        $role = Role::create(['name' => User::NORMAL]);
        $user = User::factory()->create([
            'email' => 'normal@normal.com'
        ]);

        $user->assignRole($role);
        $user->save();

        $token = $user->createToken('normal');

        $response = $this->post(
            '/api/v1/auth/register/admin',
            [
                'name' => 'John Doe',
                'email' => 'johndoe@normal.com',
                'password' => 'password',
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertForbidden();
    }
}
