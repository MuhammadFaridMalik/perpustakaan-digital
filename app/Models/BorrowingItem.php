<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowingItem extends Model
{
    use HasFactory;

    protected $fillable = ['borrowing_id', 'book_copy_id', 'returned_at', 'status', 'received_by'];

    protected function casts(): array
    {
        return [
            'returned_at' => 'date',
        ];
    }

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'dipinjam'
            && now()->toDateString() > $this->borrowing->due_date->toDateString();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_overdue ? 'Telat' : ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->status === 'dikembalikan' => 'text-primary',
            $this->status === 'telat' => 'text-danger',
            $this->is_overdue => 'text-danger',
            default => 'text-accent',
        };
    }
}
