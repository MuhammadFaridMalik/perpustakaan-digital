@extends('layouts.app')
@section('title', 'Manajemen Anggota')

@section('content')
    <p class="text-sm text-muted mb-5">Kelola data siswa yang terdaftar sebagai anggota perpustakaan.</p>

    <form method="GET" action="{{ route('members.index') }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NISN..."
               class="col-span-2 rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">

        <select name="kelas" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Kelas</option>
            @foreach ($daftarKelas as $kelas)
                <option value="{{ $kelas }}" @selected(request('kelas') === $kelas)>{{ $kelas }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded border border-border px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
        </select>

        <button type="submit" class="col-span-2 sm:col-span-4 sm:w-auto sm:justify-self-start bg-text text-white text-sm font-medium px-4 py-2 rounded">
            Terapkan Filter
        </button>
    </form>

    @if ($members->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Tidak ada anggota yang cocok dengan pencarian/filter kamu.
        </div>
    @else
        <!-- Tampilan kartu — mobile -->
        <div class="grid gap-3 lg:hidden">
            @foreach ($members as $member)
                <a href="{{ route('members.show', $member) }}" class="block border border-border rounded p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium">{{ $member->name }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $member->siswaProfile->nisn ?? '—' }} · {{ $member->siswaProfile->kelas ?? '—' }}</p>
                        </div>
                        <span class="text-xs font-medium {{ $member->is_active ? 'text-primary' : 'text-danger' }} whitespace-nowrap">
                            {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Tampilan tabel — desktop -->
        <div class="hidden lg:block border border-border rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-bg border-b border-border text-left text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">NISN</th>
                        <th class="px-4 py-3 font-medium">Kelas</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($members as $member)
                        <tr>
                            <td class="px-4 py-3">{{ $member->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $member->siswaProfile->nisn ?? '—' }}</td>
                            <td class="px-4 py-3 text-muted">{{ $member->siswaProfile->kelas ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium {{ $member->is_active ? 'text-primary' : 'text-danger' }}">
                                    {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('members.show', $member) }}" class="text-sm text-primary hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    @endif
@endsection
