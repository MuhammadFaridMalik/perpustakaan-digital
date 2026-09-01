<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\AuditLog;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\Rack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with(['category', 'author', 'publisher', 'rack'])
            ->withCount([
                'copies as total_copies',
                'copies as available_copies' => fn ($q) => $q->where('status', 'tersedia'),
                'copies as borrowed_copies' => fn ($q) => $q->where('status', 'dipinjam'),
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->where('title', 'like', '%' . $request->search . '%')
                       ->orWhere('isbn', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('author_id'), fn ($q) => $q->where('author_id', $request->author_id))
            ->when($request->filled('publisher_id'), fn ($q) => $q->where('publisher_id', $request->publisher_id))
            ->when($request->filled('rack_id'), fn ($q) => $q->where('rack_id', $request->rack_id))
            ->when($request->availability === 'tersedia', function ($q) {
                $q->whereHas('copies', fn ($qq) => $qq->where('status', 'tersedia'));
            })
            ->when($request->availability === 'habis', function ($q) {
                $q->whereDoesntHave('copies', fn ($qq) => $qq->where('status', 'tersedia'));
            })
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        return view('books.index', compact('books', 'categories', 'authors', 'publishers', 'racks'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        if ($categories->isEmpty() || $authors->isEmpty()) {
            return redirect()->route('books.index')
                ->with('error', 'Tambahkan minimal 1 kategori dan 1 pengarang di Data Master sebelum membuat buku.');
        }

        return view('books.create', compact('categories', 'authors', 'publishers', 'racks'));
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book = Book::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_book',
            'description' => "Menambah buku: {$book->title}",
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Buku berhasil ditambahkan. Sekarang tambahkan eksemplar fisiknya.');
    }

    public function show(Book $book)
    {
        $book->load(['category', 'author', 'publisher', 'rack', 'copies' => fn ($q) => $q->orderBy('inventory_code')]);

        $stock = [
            'total' => $book->copies->count(),
            'tersedia' => $book->copies->where('status', 'tersedia')->count(),
            'dipinjam' => $book->copies->where('status', 'dipinjam')->count(),
            'dipesan' => $book->copies->where('status', 'dipesan')->count(),
            'rusak' => $book->copies->where('status', 'rusak')->count(),
            'hilang' => $book->copies->where('status', 'hilang')->count(),
        ];

        return view('books.show', compact('book', 'stock'));
    }

    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        $authors = Author::orderBy('name')->get();
        $publishers = Publisher::orderBy('name')->get();
        $racks = Rack::orderBy('code')->get();

        return view('books.edit', compact('book', 'categories', 'authors', 'publishers', 'racks'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $book->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_book',
            'description' => "Mengubah data buku: {$book->title}",
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Data buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->copies()->exists()) {
            return back()->with('error', 'Buku tidak bisa dihapus karena masih memiliki eksemplar. Hapus semua eksemplar terlebih dahulu.');
        }

        $title = $book->title;

        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_book',
            'description' => "Menghapus buku: {$title}",
        ]);

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
}
