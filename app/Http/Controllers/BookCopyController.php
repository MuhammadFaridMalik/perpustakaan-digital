<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BookCopy;
use Illuminate\Http\Request;

class BookCopyController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $validated = $request->validate([
            'inventory_code' => ['required', 'string', 'max:30', 'unique:book_copies,inventory_code'],
            'condition_note' => ['nullable', 'string', 'max:150'],
        ]);

        $copy = $book->copies()->create([
            'inventory_code' => $validated['inventory_code'],
            'condition_note' => $validated['condition_note'] ?? null,
            'status' => 'tersedia',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_book_copy',
            'description' => "Menambah eksemplar {$copy->inventory_code} untuk buku: {$book->title}",
        ]);

        return back()->with('success', 'Eksemplar berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, BookCopy $copy)
    {
        if (in_array($copy->status, ['dipinjam', 'dipesan'], true)) {
            return back()->with('error', 'Status eksemplar ini dikendalikan oleh proses peminjaman/booking, tidak bisa diubah manual di sini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:tersedia,rusak,hilang'],
            'condition_note' => ['nullable', 'string', 'max:150'],
        ]);

        $copy->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_book_copy_status',
            'description' => "Mengubah status eksemplar {$copy->inventory_code} menjadi: {$copy->status}",
        ]);

        return back()->with('success', 'Status eksemplar berhasil diperbarui.');
    }

    public function destroy(BookCopy $copy)
    {
        if ($copy->status !== 'tersedia') {
            return back()->with('error', 'Hanya eksemplar berstatus "tersedia" yang bisa dihapus.');
        }

        if ($copy->borrowingItems()->exists()) {
            return back()->with('error', 'Eksemplar tidak bisa dihapus karena memiliki riwayat peminjaman.');
        }

        $code = $copy->inventory_code;
        $bookTitle = $copy->book->title;
        $copy->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_book_copy',
            'description' => "Menghapus eksemplar {$code} dari buku: {$bookTitle}",
        ]);

        return back()->with('success', 'Eksemplar berhasil dihapus.');
    }
}
