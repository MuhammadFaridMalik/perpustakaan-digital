@extends('layouts.app')
@section('title', 'Data Master — Rak')

@section('content')
    <div x-data="{ showAdd: false, editId: null }">
        <div class="flex items-center justify-between mb-5">
            <p class="text-sm text-muted">Rak menandai lokasi fisik buku di perpustakaan.</p>
            <button @click="showAdd = true" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded">
                + Tambah Rak
            </button>
        </div>

        @if ($racks->isEmpty())
            <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
                Belum ada data rak.
            </div>
        @else
            <div class="border border-border rounded overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-bg border-b border-border text-left text-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="px-4 py-3 font-medium">Jumlah Buku</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($racks as $rack)
                            <tr>
                                <td class="px-4 py-3 font-mono">{{ $rack->code }}</td>
                                <td class="px-4 py-3 text-muted">{{ $rack->description ?? '—' }}</td>
                                <td class="px-4 py-3 text-muted">{{ $rack->books_count }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    <button @click="editId = {{ $rack->id }}" class="text-sm text-primary hover:underline">Ubah</button>
                                    <form method="POST" action="{{ route('racks.destroy', $rack) }}" class="inline"
                                          onsubmit="return confirm('Hapus rak ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm text-danger hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <div x-show="editId === {{ $rack->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
                                <div class="absolute inset-0 bg-black/40" @click="editId = null"></div>
                                <div class="relative bg-surface rounded border border-border p-6 w-full max-w-sm">
                                    <h4 class="font-medium mb-4">Ubah Rak</h4>
                                    <form method="POST" action="{{ route('racks.update', $rack) }}" class="space-y-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="code" value="{{ $rack->code }}" required
                                               class="w-full rounded border border-border px-3 py-2 text-sm">
                                        <input type="text" name="description" value="{{ $rack->description }}"
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

        <div x-show="showAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" @click="showAdd = false"></div>
            <div class="relative bg-surface rounded border border-border p-6 w-full max-w-sm">
                <h4 class="font-medium mb-4">Tambah Rak</h4>
                @if ($errors->any())
                    <div class="mb-3 text-sm text-danger">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('racks.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="code" required placeholder="Contoh: A1"
                           class="w-full rounded border border-border px-3 py-2 text-sm">
                    <input type="text" name="description" placeholder="Deskripsi (opsional)"
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
