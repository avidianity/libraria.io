<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetch authors.
     * 
     * @return void
     */
    public function testFetchAuthors()
    {
        $response = $this->get('/api/v1/authors', [
            'Accept' => 'application/json'
        ]);

        $response->assertOk();
    }

    /**
     * Test create author.
     * 
     * @return void
     */
    public function testCreateAuthor()
    {
        $user = User::factory()
            ->create();

        $token = $user->createToken('normal');

        $response = $this->post('/api/v1/authors', [
            'website' => 'https://author.com',
            'address' => 'Address, Country',
            'email' => $user->email,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertCreated();
    }

    /**
     * Test create author without authentication.
     * 
     * @return void
     */
    public function testCreateAuthorWithoutAuthentication()
    {
        $response = $this->post('/api/v1/authors', [
            'website' => 'https://author.com',
            'address' => 'Address, Country',
            'email' => 'email@email.com',
        ], [
            'Accept' => 'application/json',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test update author.
     * 
     * @return void
     */
    public function testUpdateAuthorAsAdmin()
    {
        $user = User::factory()
            ->create();

        $user->assignRole(Role::create(['name' => User::ADMIN]));
        $user->save();

        $token = $user->createToken('normal');

        $author = Author::factory()
            ->create(['user_id' => $user->id]);

        $response = $this->put("/api/v1/authors/{$author->id}", [
            'website' => 'https://author.com',
            'address' => 'Address, Country',
            'email' => $user->email,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertOk();
    }

    /**
     * Test update unowned author as a normal user.
     * 
     * @return void
     */
    public function testUpdateUnownedAuthorAsNormalUser()
    {
        $user = User::factory()
            ->create();

        $user->assignRole(Role::create(['name' => User::NORMAL]));
        $user->save();

        $token = $user->createToken('normal');

        $author = Author::factory()
            ->create(['user_id' => User::factory()->create()->id]);

        $response = $this->put("/api/v1/authors/{$author->id}", [
            'website' => 'https://author.com',
            'address' => 'Address, Country',
            'email' => $user->email,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertForbidden();
    }

    /**
     * Test update unowned author as an admin.
     * 
     * @return void
     */
    public function testUpdateUnownedAuthorAsAdmin()
    {
        $user = User::factory()
            ->create();

        $user->assignRole(Role::create(['name' => User::ADMIN]));
        $user->save();

        $token = $user->createToken('normal');

        $author = Author::factory()
            ->create(['user_id' => User::factory()->create()->id]);

        $response = $this->put("/api/v1/authors/{$author->id}", [
            'website' => 'https://author.com',
            'address' => 'Address, Country',
            'email' => $user->email,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertOk();
    }

    /**
     * Test delete author.
     * 
     * @return void
     */
    public function testDeleteAuthor()
    {
        $user = User::factory()
            ->create();

        $token = $user->createToken('normal');

        $author = Author::factory()
            ->create(['user_id' => $user->id]);

        $response = $this->delete("/api/v1/authors/{$author->id}", [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertNoContent();
    }
}
