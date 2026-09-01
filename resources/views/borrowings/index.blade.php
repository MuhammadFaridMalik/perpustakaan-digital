@extends('layouts.app')
@section('title', 'Peminjaman')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <p class="text-sm text-muted">Daftar transaksi peminjaman buku fisik.</p>
        <a href="{{ route('borrowings.create') }}"
           class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors whitespace-nowrap">
            + Buat Peminjaman
        </a>
    </div>

    <form method="GET" action="{{ route('borrowings.index') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa / judul buku..."
               class="col-span-2 rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <select name="status" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
            <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
            <option value="telat" @selected(request('status') === 'telat')>Telat</option>
        </select>

        <button type="submit" class="bg-text text-white text-sm font-medium px-4 py-2 rounded">Terapkan</button>
    </form>

    @if ($items->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Tidak ada transaksi peminjaman yang cocok.
        </div>
    @else
        <!-- Kartu — mobile -->
        <div class="grid gap-3 lg:hidden">
            @foreach ($items as $item)
                <div class="border border-border rounded p-4">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div>
                            <p class="font-medium text-sm">{{ $item->bookCopy->book->title }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $item->borrowing->siswa->name }}</p>
                        </div>
                        <span class="text-xs font-medium {{ $item->status_color }} whitespace-nowrap">{{ $item->status_label }}</span>
                    </div>
                    <p class="text-xs text-muted mb-3">Jatuh tempo: {{ $item->borrowing->due_date->format('d M Y') }}</p>
                    @if ($item->status === 'dipinjam')
                        <form method="POST" action="{{ route('borrowing-items.return', $item) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                            @csrf
                            @method('PATCH')
                            <button class="text-sm text-primary hover:underline">Proses Pengembalian</button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Tabel — desktop -->
        <div class="hidden lg:block border border-border rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-bg border-b border-border text-left text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Siswa</th>
                        <th class="px-4 py-3 font-medium">Buku</th>
                        <th class="px-4 py-3 font-medium">Jatuh Tempo</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->borrowing->siswa->name }}</td>
                            <td class="px-4 py-3">{{ $item->bookCopy->book->title }}</td>
                            <td class="px-4 py-3 text-muted">{{ $item->borrowing->due_date->format('d M Y') }}</td>
                            <td class="px-4 py-3"><span class="text-xs font-medium {{ $item->status_color }}">{{ $item->status_label }}</span></td>
                            <td class="px-4 py-3 text-right">
                                @if ($item->status === 'dipinjam')
                                    <form method="POST" action="{{ route('borrowing-items.return', $item) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-sm text-primary hover:underline">Proses Pengembalian</button>
                                    </form>
                                @else
                                    <span class="text-muted text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
@endsection
