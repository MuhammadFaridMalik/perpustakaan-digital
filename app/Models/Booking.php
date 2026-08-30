<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['siswa_id', 'book_id', 'status', 'booked_at', 'expires_at', 'processed_by'];

    protected function casts(): array
    {
        return [
            'booked_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
