<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Register events after booting.
     * 
     * @return void
     */
    public static function booted()
    {
        static::deleted(function ($tag) {
            $books = Book::all();

            foreach ($books as $book) {
                $ids = $book->tag_ids;
                if (in_array($tag->id, $ids)) {
                    $ids = collect($ids)
                        ->filter(function ($id) use ($tag) {
                            return $id !== $tag->id;
                        })->all();
                    $book->tag_ids = $ids;
                    $book->save();
                }
            }
        });
    }
}
