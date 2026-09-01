<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('books')->orderBy('name')->get();

        return view('master.authors', compact('authors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $author = Author::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_author',
            'description' => "Menambah pengarang: {$author->name}",
        ]);

        return back()->with('success', 'Pengarang berhasil ditambahkan.');
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $author->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_author',
            'description' => "Mengubah pengarang menjadi: {$author->name}",
        ]);

        return back()->with('success', 'Pengarang berhasil diperbarui.');
    }

    public function destroy(Author $author)
    {
        if ($author->books()->exists()) {
            return back()->with('error', 'Pengarang tidak bisa dihapus karena masih dipakai oleh buku.');
        }

        $name = $author->name;
        $author->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_author',
            'description' => "Menghapus pengarang: {$name}",
        ]);

        return back()->with('success', 'Pengarang berhasil dihapus.');
    }
}
