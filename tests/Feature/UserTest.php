<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test fetch users.
     *
     * @return void
     */
    public function testFetchUsers()
    {
        $response = $this->get('/api/v1/users');

        $response->assertStatus(200);
    }

    /**
     * Test create user.
     * 
     * @return void
     */
    public function testCreateUser()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => User::ADMIN]));
        Role::create(['name' => User::NORMAL]);
        $user->save();
        $token = $user->createToken(User::ADMIN);

        $response = $this->post(
            '/api/v1/users',
            [
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'password' => $this->faker->text,
                'role' => collect([User::ADMIN, User::NORMAL])
                    ->random(1)[0]
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertCreated();
    }

    /**
     * Test update user.
     * 
     * @return void
     */
    public function testUpdateUser()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(User::ADMIN));
        Role::create(['name' => User::NORMAL]);
        $token = $user->createToken(User::ADMIN);

        $toUpdateUser = User::factory()->create();

        $response = $this->put(
            "/api/v1/users/{$toUpdateUser->id}",
            [
                'name' => $this->faker->name,
                'email' => $this->faker->safeEmail,
                'password' => $this->faker->text,
                'role' => collect([User::ADMIN, User::NORMAL])
                    ->random(1)[0]
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertOk();
    }

    /**
     * Test delete user.
     * 
     * @return void
     */
    public function testDeleteUser()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate(User::ADMIN));
        $token = $user->createToken(User::ADMIN);

        $toDeleteUser = User::factory()->create();

        $response = $this->delete(
            "/api/v1/users/{$toDeleteUser->id}",
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertNoContent();
    }
}
