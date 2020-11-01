<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['website', 'address', 'email', 'user_id'];

    /**
     * Get the corresponding user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get author's books.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
