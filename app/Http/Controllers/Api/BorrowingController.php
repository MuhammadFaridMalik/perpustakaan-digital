<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BorrowingItem;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    public function index()
    {
        $items = BorrowingItem::with('bookCopy.book')
            ->whereHas('borrowing', fn ($q) => $q->where('siswa_id', Auth::id()))
            ->latest('created_at')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'book_title' => $item->bookCopy->book->title,
                'borrowed_at' => $item->borrowing->borrowed_at->toDateString(),
                'due_date' => $item->borrowing->due_date->toDateString(),
                'returned_at' => $item->returned_at?->toDateString(),
                'status' => $item->status_label,
            ]);

        return response()->json(['success' => true, 'data' => $items]);
    }
}
