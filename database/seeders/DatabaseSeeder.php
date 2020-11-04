<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\File;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->create([
                'email' => 'admin@admin.com',
            ]);

        Role::findOrCreate(User::ADMIN);
        Role::findOrCreate(User::NORMAL);
        Category::factory(10)->create();
        Tag::factory(10)->create();

        User::all()
            ->each(function ($user) {
                $user->assignRole(Role::all()
                    ->random(1)[0]);
                $author = Author::factory()->makeOne();
                $user->authors()->save($author);
                $tags = Tag::all()->random(3)
                    ->map(function ($tag) {
                        return $tag->id;
                    })->all();
                $author->books()
                    ->save(Book::factory(5)
                        ->makeOne([
                            'category_id' => Category::all()
                                ->random(1)[0]->id,
                            'photo_id' => File::factory()->create()->id,
                            'tag_ids' => $tags,
                        ]));
                $user->save();
            });
    }
}
