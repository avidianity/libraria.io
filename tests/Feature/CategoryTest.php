<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test fetch categories
     *
     * @return void
     */
    public function testFetchCategories()
    {
        $response = $this->get('/api/v1/categories', [
            'Accept' => 'application/json'
        ]);

        $this->assertIsArray($response->json());
    }

    /**
     * Test create category.
     * 
     * @return void
     */
    public function testCreateCategory()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::ADMIN]));
        $user->save();

        $token = $user->createToken('admin');

        $response = $this->post('/api/v1/categories', [
            'name' => $this->faker->name,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertCreated();
    }

    /**
     * Test update category.
     * 
     * @return void
     */
    public function testUpdateCategory()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::ADMIN]));
        $user->save();

        $token = $user->createToken('admin');

        $category = Category::factory()->create();

        $response = $this->put("/api/v1/categories/{$category->id}", [
            'name' => $this->faker->name,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertOk();
    }

    /**
     * Test delete category.
     * 
     * @return void
     */
    public function testDeleteCategory()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::ADMIN]));
        $user->save();

        $token = $user->createToken('admin');

        $category = Category::factory()->create();

        $response = $this->delete(
            "/api/v1/categories/{$category->id}",
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertNoContent();
    }
}
