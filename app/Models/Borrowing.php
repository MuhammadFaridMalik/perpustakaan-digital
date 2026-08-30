<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = ['siswa_id', 'admin_id', 'borrowed_at', 'due_date'];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'date',
            'due_date' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function items()
    {
        return $this->hasMany(BorrowingItem::class);
    }
}
