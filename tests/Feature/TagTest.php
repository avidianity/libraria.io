<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test fetch categories
     *
     * @return void
     */
    public function testFetchTags()
    {
        $response = $this->get('/api/v1/tags', [
            'Accept' => 'application/json'
        ]);

        $this->assertIsArray($response->json());
    }

    /**
     * Test create tag.
     * 
     * @return void
     */
    public function testCreateTags()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::NORMAL]));
        $user->save();

        $token = $user->createToken('normal');

        $response = $this->post('/api/v1/tags', [
            'name' => $this->faker->name,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertCreated();
    }

    /**
     * Test update tag.
     * 
     * @return void
     */
    public function testUpdateTag()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::NORMAL]));
        $user->save();

        $token = $user->createToken('normal');

        $tag = Tag::factory()->create();

        $response = $this->put("/api/v1/tags/{$tag->id}", [
            'name' => $this->faker->name,
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $response->assertOk();
    }

    /**
     * Test delete tag.
     * 
     * @return void
     */
    public function testDeleteTag()
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => USER::NORMAL]));
        $user->save();

        $token = $user->createToken('normal');

        $tag = Tag::factory()->create();

        $response = $this->delete(
            "/api/v1/tags/{$tag->id}",
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$token->plainTextToken}"
            ]
        );

        $response->assertNoContent();
    }
}
