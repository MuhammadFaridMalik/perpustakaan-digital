@extends('layouts.app')
@section('title', 'Data Master — Kategori')

@section('content')
    <div x-data="{ showAdd: false, editId: null }">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-muted">Kategori buku dipakai untuk klasifikasi & filter pencarian.</p>
            <button @click="showAdd = true" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded">
                + Tambah Kategori
            </button>
        </div>

        @if ($categories->isEmpty())
            <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
                Belum ada kategori. Tambahkan minimal 1 sebelum membuat buku.
            </div>
        @else
            <div class="border border-border rounded overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-bg border-b border-border text-left text-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Jumlah Buku</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-4 py-3">{{ $category->name }}</td>
                                <td class="px-4 py-3 text-muted">{{ $category->books_count }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <button @click="editId = {{ $category->id }}" class="text-sm text-primary hover:underline">Ubah</button>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline"
                                          onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-danger hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Modal Ubah -->
                            <div x-show="editId === {{ $category->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                                <div class="absolute inset-0 bg-black/40" @click="editId = null"></div>
                                <div class="relative bg-surface rounded border border-border p-6 w-full max-w-sm">
                                    <h4 class="font-medium mb-4">Ubah Kategori</h4>
                                    <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $category->name }}" required
                                               class="w-full rounded border border-border px-3 py-2 text-sm">
                                        <div class="flex gap-3 pt-2">
                                            <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded">Simpan</button>
                                            <button type="button" @click="editId = null" class="text-sm text-muted px-4 py-2">Batal</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Modal Tambah -->
        <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
            <div class="relative bg-surface rounded border border-border p-6 w-full max-w-sm">
                <h4 class="font-medium mb-4">Tambah Kategori</h4>
                @if ($errors->any())
                    <div class="mb-3 text-sm text-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" required placeholder="Contoh: Fiksi"
                           class="w-full rounded border border-border px-3 py-2 text-sm">
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded">Simpan</button>
                        <button type="button" @click="showAdd = false" class="text-sm text-muted px-4 py-2">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
