@extends('layouts.app')
@section('title', 'Kelola Buku')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <p class="text-sm text-muted">Kelola judul buku dan eksemplar fisiknya.</p>
        <a href="{{ route('books.create') }}"
           class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors whitespace-nowrap">
            + Tambah Buku
        </a>
    </div>

    <form method="GET" action="{{ route('books.index') }}" class="grid grid-cols-2 sm:grid-cols-6 gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / ISBN..."
               class="col-span-2 sm:col-span-2 rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <select name="category_id" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>

        <select name="author_id" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Pengarang</option>
            @foreach ($authors as $author)
                <option value="{{ $author->id }}" @selected(request('author_id') == $author->id)>{{ $author->name }}</option>
            @endforeach
        </select>

        <select name="rack_id" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Rak</option>
            @foreach ($racks as $rack)
                <option value="{{ $rack->id }}" @selected(request('rack_id') == $rack->id)>{{ $rack->code }}</option>
            @endforeach
        </select>

        <select name="availability" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="tersedia" @selected(request('availability') === 'tersedia')>Tersedia</option>
            <option value="habis" @selected(request('availability') === 'habis')>Stok Habis</option>
        </select>

        <button type="submit" class="col-span-2 sm:col-span-1 bg-text text-white text-sm font-medium px-4 py-2 rounded">
            Terapkan
        </button>
    </form>

    @if ($books->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Tidak ada buku yang cocok dengan pencarian/filter kamu.
        </div>
    @else
        <div class="border border-border rounded overflow-hidden overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-bg border-b border-border text-left text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Judul</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Rak</th>
                        <th class="px-4 py-3 font-medium">Stok</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($books as $book)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $book->title }}</p>
                                <p class="text-xs text-muted">{{ $book->author->name }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $book->category->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $book->rack->code ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs {{ $book->available_copies > 0 ? 'text-primary' : 'text-danger' }} font-medium">
                                    {{ $book->available_copies }} tersedia
                                </span>
                                <span class="text-xs text-muted"> / {{ $book->total_copies }} total</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('books.show', $book) }}" class="text-sm text-primary hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $books->links() }}
        </div>
    @endif
@endsection
