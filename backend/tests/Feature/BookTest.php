<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test fetch books
     *
     * @return void
     */
    public function testFetchBooks()
    {
        $response = $this->get('/api/v1/books', [
            'Accept' => 'application/json'
        ]);

        $this->assertArrayHasKey('data', $response->json());
    }

    /**
     * Test create book.
     * 
     * @return void
     */
    public function testCreateBook()
    {
        $user = User::factory()->create();
        $author = Author::factory()
            ->create(['user_id' => $user->id]);
        $category = Category::factory()
            ->create();

        $token = $user->createToken('admin');

        $response = $this->post('/api/v1/books', [
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'category_id' => $category->id,
            'author_id' => $author->id,
            'photo' => UploadedFile::fake()->create('picture', 150, 'image/png'),
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        File::find($response->json()['photo_id'])->delete();

        $response->assertCreated();
    }

    /**
     * Test update book.
     * 
     * @return void
     */
    public function testUpdateBook()
    {
        $user = User::factory()->create();
        $author = Author::factory()
            ->create(['user_id' => $user->id]);
        $category = Category::factory()
            ->create();

        $token = $user->createToken('admin');

        $photo = File::factory()->create();

        $book = Book::factory()
            ->create([
                'category_id' => $category->id,
                'author_id' => $author->id,
                'photo_id' => $photo->id,
            ]);

        $response = $this->post("/api/v1/books/{$book->id}", [
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'category_id' => $category->id,
            'author_id' => $author->id,
            'photo' => UploadedFile::fake()->create('picture', 150, 'image/png'),
            '_method' => 'PUT'
        ], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $photo->delete();

        File::find($response->json()['photo_id'])->delete();

        $response->assertOk();
    }

    /**
     * Test delete book.
     * 
     * @return void
     */
    public function testDeleteBook()
    {
        $user = User::factory()->create();
        $author = Author::factory()
            ->create(['user_id' => $user->id]);
        $category = Category::factory()
            ->create();

        $token = $user->createToken('admin');

        $photo = File::factory()->create();

        $book = Book::factory()
            ->create([
                'category_id' => $category->id,
                'author_id' => $author->id,
                'photo_id' => $photo->id,
            ]);

        $response = $this->delete("/api/v1/books/{$book->id}", [], [
            'Accept' => 'application/json',
            'Authorization' => "Bearer {$token->plainTextToken}"
        ]);

        $photo->delete();

        $response->assertNoContent();
    }
}
