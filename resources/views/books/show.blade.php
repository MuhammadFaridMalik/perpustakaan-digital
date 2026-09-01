@extends('layouts.app')
@section('title', 'Detail Buku')

@section('content')
    <div x-data="{ showAddCopy: false }">
        @if (session('error'))
            <div class="mb-6 rounded border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8 mb-10">
            <div class="w-full lg:w-64 shrink-0">
                @if ($book->cover_image)
                    <img src="{{ Storage::url($book->cover_image) }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover rounded border border-border">
                @else
                    <div class="w-full aspect-[3/4] rounded border border-dashed border-border flex items-center justify-center text-muted text-sm">
                        Tidak ada cover
                    </div>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-1">
                    <h2 class="text-xl font-semibold break-words">{{ $book->title }}</h2>
                    <div class="flex gap-4 shrink-0">
                        <a href="{{ route('books.edit', $book) }}" class="text-sm text-primary hover:underline">Ubah</a>
                        <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('Hapus buku ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-sm text-danger hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
                <p class="text-muted mb-4">{{ $book->author->name }}</p>

                <dl class="grid grid-cols-2 gap-y-2 text-sm mb-4">
                    <dt class="text-muted">Kategori</dt>
                    <dd>{{ $book->category->name }}</dd>
                    <dt class="text-muted">Penerbit</dt>
                    <dd>{{ $book->publisher->name ?? '—' }}</dd>
                    <dt class="text-muted">Tahun Terbit</dt>
                    <dd>{{ $book->published_year ?? '—' }}</dd>
                    <dt class="text-muted">ISBN</dt>
                    <dd>{{ $book->isbn ?? '—' }}</dd>
                    <dt class="text-muted">Lokasi Rak</dt>
                    <dd>{{ $book->rack->code ?? 'Belum ditentukan' }}</dd>
                </dl>

                @if ($book->synopsis)
                    <p class="text-sm leading-relaxed">{{ $book->synopsis }}</p>
                @endif
            </div>
        </div>

        <!-- Ringkasan stok: strip scroll horizontal di mobile, grid rapi di desktop -->
        <div class="flex sm:grid sm:grid-cols-5 gap-3 mb-8 overflow-x-auto sm:overflow-visible -mx-4 px-4 sm:mx-0 sm:px-0">
            <div class="border border-border rounded p-4 min-w-[128px] sm:min-w-0 shrink-0">
                <p class="text-xs text-muted whitespace-nowrap">Total Eksemplar</p>
                <p class="text-xl font-semibold mt-1">{{ $stock['total'] }}</p>
            </div>
            <div class="border border-border rounded p-4 min-w-[128px] sm:min-w-0 shrink-0">
                <p class="text-xs text-muted whitespace-nowrap">Tersedia</p>
                <p class="text-xl font-semibold mt-1 text-primary">{{ $stock['tersedia'] }}</p>
            </div>
            <div class="border border-border rounded p-4 min-w-[128px] sm:min-w-0 shrink-0">
                <p class="text-xs text-muted whitespace-nowrap">Dipinjam</p>
                <p class="text-xl font-semibold mt-1">{{ $stock['dipinjam'] }}</p>
            </div>
            <div class="border border-border rounded p-4 min-w-[128px] sm:min-w-0 shrink-0">
                <p class="text-xs text-muted whitespace-nowrap">Dipesan</p>
                <p class="text-xl font-semibold mt-1">{{ $stock['dipesan'] }}</p>
            </div>
            <div class="border border-border rounded p-4 min-w-[128px] sm:min-w-0 shrink-0">
                <p class="text-xs text-muted whitespace-nowrap">Rusak/Hilang</p>
                <p class="text-xl font-semibold mt-1 text-danger">{{ $stock['rusak'] + $stock['hilang'] }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-medium">Eksemplar Fisik</h3>
            <button @click="showAddCopy = true" class="text-sm text-primary hover:underline">+ Tambah Eksemplar</button>
        </div>

        @if ($book->copies->isEmpty())
            <div class="border border-dashed border-border rounded p-8 text-center text-muted text-sm">
                Belum ada eksemplar fisik untuk buku ini.
            </div>
        @else
            @php
                $statusColorMap = [
                    'tersedia' => 'text-primary',
                    'dipinjam' => 'text-accent',
                    'dipesan' => 'text-accent',
                ];
            @endphp

            <!-- Tampilan kartu — mobile -->
            <div class="grid gap-3 lg:hidden">
                @foreach ($book->copies as $copy)
                    <div class="border border-border rounded p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <p class="font-mono text-sm">{{ $copy->inventory_code }}</p>
                            <span class="text-xs font-medium {{ $statusColorMap[$copy->status] ?? 'text-danger' }} capitalize whitespace-nowrap">
                                {{ $copy->status }}
                            </span>
                        </div>
                        @if ($copy->condition_note)
                            <p class="text-xs text-muted mb-3">{{ $copy->condition_note }}</p>
                        @endif
                        <div class="flex items-center gap-4">
                            @if (in_array($copy->status, ['tersedia', 'rusak', 'hilang']))
                                <form method="POST" action="{{ route('book-copies.update-status', $copy) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs border border-border rounded px-2 py-1">
                                        <option value="tersedia" @selected($copy->status === 'tersedia')>Tersedia</option>
                                        <option value="rusak" @selected($copy->status === 'rusak')>Rusak</option>
                                        <option value="hilang" @selected($copy->status === 'hilang')>Hilang</option>
                                    </select>
                                </form>
                            @endif
                            @if ($copy->status === 'tersedia')
                                <form method="POST" action="{{ route('book-copies.destroy', $copy) }}"
                                      onsubmit="return confirm('Hapus eksemplar ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-xs text-danger hover:underline">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tampilan tabel — desktop -->
            <div class="hidden lg:block border border-border rounded overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-bg border-b border-border text-left text-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kode Inventaris</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Catatan Kondisi</th>
                            <th class="px-4 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($book->copies as $copy)
                            <tr>
                                <td class="px-4 py-3 font-mono">{{ $copy->inventory_code }}</td>
                                <td class="px-4 py-3">
                                    <span class="text-xs font-medium {{ $statusColorMap[$copy->status] ?? 'text-danger' }} capitalize">{{ $copy->status }}</span>
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $copy->condition_note ?? '—' }}</td>
                                <td class="px-4 py-3 text-right space-x-3">
                                    @if (in_array($copy->status, ['tersedia', 'rusak', 'hilang']))
                                        <form method="POST" action="{{ route('book-copies.update-status', $copy) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()" class="text-xs border border-border rounded px-2 py-1">
                                                <option value="tersedia" @selected($copy->status === 'tersedia')>Tersedia</option>
                                                <option value="rusak" @selected($copy->status === 'rusak')>Rusak</option>
                                                <option value="hilang" @selected($copy->status === 'hilang')>Hilang</option>
                                            </select>
                                        </form>
                                    @endif
                                    @if ($copy->status === 'tersedia')
                                        <form method="POST" action="{{ route('book-copies.destroy', $copy) }}" class="inline"
                                              onsubmit="return confirm('Hapus eksemplar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-xs text-danger hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Modal Tambah Eksemplar -->
        <div x-show="showAddCopy" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/40" @click="showAddCopy = false"></div>
            <div class="relative bg-surface rounded border border-border p-6 w-full max-w-sm">
                <h4 class="font-medium mb-4">Tambah Eksemplar Baru</h4>

                @if ($errors->any())
                    <div class="mb-3 text-sm text-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('book-copies.store', $book) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium mb-1">Kode Inventaris</label>
                        <input type="text" name="inventory_code" required placeholder="Contoh: BM-003"
                               class="w-full rounded border border-border px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan Kondisi (opsional)</label>
                        <input type="text" name="condition_note"
                               class="w-full rounded border border-border px-3 py-2 text-sm">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded">
                            Simpan
                        </button>
                        <button type="button" @click="showAddCopy = false" class="text-sm text-muted px-4 py-2">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
