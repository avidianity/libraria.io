<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user authentication service with correct credentials.
     *
     * @return void
     */
    public function testAuthServiceWithCorrectCredentials()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $service = new AuthService([
            'email' => 'admin@admin.com',
            'password' => 'password'
        ]);

        $result = $service->authenticate();

        $this->assertTrue($result, 'User is authenticated.');
    }

    /**
     * Test user authentication service with incorrect credentials.
     *
     * @return void
     */
    public function testAuthServiceWithIncorrectCredentials()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $service = new AuthService([
            'email' => 'admin@admin.com',
            'password' => 'wrong password'
        ]);

        $result = $service->authenticate();

        $this->assertNotTrue($result, $service->getMessage());
    }

    /**
     * Test user authentication service with non-existing credentials.
     *
     * @return void
     */
    public function testAuthServiceWithNonExistingCredentials()
    {
        $service = new AuthService([
            'email' => 'admin@admin.com',
            'password' => 'password'
        ]);

        $result = $service->authenticate();

        $this->assertNotTrue($result, $service->getMessage());
    }

    /**
     * Test user authentication service with roles.
     * 
     * @return void
     */
    public function testAuthServiceWithRolesAndCorrectCredentials()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        $user->assignRole(Role::create(['name' => User::ADMIN]));
        $user->save();

        $service = new AuthService();

        $result = $service->withData([
            'email' => 'admin@admin.com',
            'password' => 'password'
        ])
            ->withRole(User::ADMIN)
            ->authenticate();

        $this->assertTrue($result, 'User is authenticated.');
    }

    /**
     * Test user authentication service with roles.
     * 
     * @return void
     */
    public function testAuthServiceWithRolesAndIncorrectCredentials()
    {
        $user = new User([
            'name' => 'John Doe',
            'email' => 'normal@normal.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);

        Role::create(['name' => User::ADMIN]);
        Role::create(['name' => User::NORMAL]);

        $user->assignRole(User::NORMAL);
        $user->save();

        $service = new AuthService();

        $result = $service->withData([
            'email' => 'normal@normal.com',
            'password' => 'password'
        ])
            ->withRole(User::ADMIN)
            ->authenticate();

        $this->assertNotTrue($result, 'User is unauthenticated.');
    }
}
