<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'inventory_code', 'status', 'condition_note'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function borrowingItems()
    {
        return $this->hasMany(BorrowingItem::class);
    }
}
