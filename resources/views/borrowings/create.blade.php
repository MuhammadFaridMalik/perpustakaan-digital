@extends('layouts.app')
@section('title', 'Buat Peminjaman')

@section('content')
    <div class="max-w-lg">
        <p class="text-sm text-muted mb-5">Untuk siswa yang datang langsung tanpa booking.</p>

        @if ($errors->any())
            <div class="mb-4 rounded border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('borrowings.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">NISN Siswa</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" required placeholder="Masukkan NISN"
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Pilih Buku (bisa lebih dari satu)</label>
                @if ($books->isEmpty())
                    <p class="text-sm text-muted border border-dashed border-border rounded p-4">Tidak ada buku dengan stok tersedia saat ini.</p>
                @else
                    <select name="book_ids[]" multiple required size="6"
                            class="w-full rounded border border-border px-3 py-2 text-sm">
                        @foreach ($books as $book)
                            <option value="{{ $book->id }}" @selected(collect(old('book_ids'))->contains($book->id))>
                                {{ $book->title }} ({{ $book->available_copies }} tersedia)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-muted mt-1">Tahan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu buku.</p>
                @endif
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                    Proses Peminjaman
                </button>
                <a href="{{ route('borrowings.index') }}" class="text-sm text-muted px-4 py-2">Batal</a>
            </div>
        </form>
    </div>
@endsection
