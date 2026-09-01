@extends('layouts.app')
@section('title', 'Ubah Buku')

@section('content')
    <div class="max-w-xl">
        @if ($errors->any())
            <div class="mb-4 rounded border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Judul Buku</label>
                <input type="text" name="title" value="{{ old('title', $book->title) }}" required
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">ISBN (opsional)</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                           class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tahun Terbit (opsional)</label>
                    <input type="number" name="published_year" value="{{ old('published_year', $book->published_year) }}" min="1900" max="{{ date('Y') }}"
                           class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Kategori</label>
                    <select name="category_id" required class="w-full rounded border border-border px-3 py-2 text-sm">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pengarang</label>
                    <select name="author_id" required class="w-full rounded border border-border px-3 py-2 text-sm">
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" @selected(old('author_id', $book->author_id) == $author->id)>{{ $author->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Penerbit (opsional)</label>
                    <select name="publisher_id" class="w-full rounded border border-border px-3 py-2 text-sm">
                        <option value="">Belum diketahui</option>
                        @foreach ($publishers as $publisher)
                            <option value="{{ $publisher->id }}" @selected(old('publisher_id', $book->publisher_id) == $publisher->id)>{{ $publisher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rak (opsional)</label>
                    <select name="rack_id" class="w-full rounded border border-border px-3 py-2 text-sm">
                        <option value="">Belum ditentukan</option>
                        @foreach ($racks as $rack)
                            <option value="{{ $rack->id }}" @selected(old('rack_id', $book->rack_id) == $rack->id)>{{ $rack->code }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Sinopsis (opsional)</label>
                <textarea name="synopsis" rows="4"
                          class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">{{ old('synopsis', $book->synopsis) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Cover Buku (kosongkan jika tidak diubah)</label>
                @if ($book->cover_image)
                    <img src="{{ Storage::url($book->cover_image) }}" alt="Cover saat ini" class="w-20 h-28 object-cover rounded border border-border mb-2">
                @endif
                <input type="file" name="cover_image" accept="image/*" class="w-full text-sm">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('books.show', $book) }}" class="text-sm text-muted px-4 py-2">Batal</a>
            </div>
        </form>
    </div>
@endsection
