<?php

namespace App\Models;

use App\Casts\JSONCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'tag_ids',
        'category_id',
        'author_id',
        'photo_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'tag_ids' => JSONCast::class,
    ];

    /**
     * The attributes that should be appended for arrays.
     *
     * @var array
     */
    protected $appends = ['tags'];

    /**
     * Register model events after booting.
     * 
     * @return void
     */
    public static function booted()
    {
        static::deleted(function ($book) {
            $book->photo->delete();
        });
    }

    /**
     * Get the tags of this book.
     * @return Tag[]
     */
    public function getTagsAttribute()
    {
        if (isset($this->attributes['tag_ids']) && is_string($this->attributes['tag_ids'])) {
            $tags = [];
            $ids = json_decode($this->attributes['tag_ids']);

            foreach ($ids as $id) {
                $tags[] = Tag::find($id);
            }

            return $tags;
        }
        return [];
    }

    public function photo()
    {
        return $this->belongsTo(File::class, 'photo_id');
    }

    /**
     * Get the category of this book.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the author of this book.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
