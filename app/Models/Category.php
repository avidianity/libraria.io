<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
     * Register model events after booting.
     * 
     * @return void
     */
    public static function booted()
    {
        static::deleting(function ($category) {
            $category->books->each(function ($book) {
                $book->delete();
            });
        });
    }

    /**
     * Get all books of this category.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
