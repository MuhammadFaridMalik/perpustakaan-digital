<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'isbn', 'synopsis', 'category_id', 'author_id',
        'publisher_id', 'rack_id', 'published_year', 'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
