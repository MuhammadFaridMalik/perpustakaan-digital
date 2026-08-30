@extends('layouts.app')
@section('title', 'Manajemen Admin')

@section('content')
    <div class="flex items-center justify-between mb-5">
        <p class="text-sm text-muted">Kelola akun Admin operasional perpustakaan.</p>
        <a href="{{ route('admins.create') }}"
           class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
            + Tambah Admin
        </a>
    </div>

    @if ($admins->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Belum ada akun admin. Klik "Tambah Admin" untuk membuat yang pertama.
        </div>
    @else
        <div class="border border-border rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-bg border-b border-border text-left text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Username</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($admins as $admin)
                        <tr>
                            <td class="px-4 py-3">{{ $admin->name }}</td>
                            <td class="px-4 py-3 text-muted">{{ $admin->username }}</td>
                            <td class="px-4 py-3">
                                @if ($admin->is_active)
                                    <span class="text-primary text-xs font-medium">Aktif</span>
                                @else
                                    <span class="text-danger text-xs font-medium">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-3">
                                <a href="{{ route('admins.edit', $admin) }}" class="text-sm text-primary hover:underline">Ubah</a>
                                <form method="POST" action="{{ route('admins.toggle-active', $admin) }}" class="inline"
                                      onsubmit="return confirm('{{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-sm {{ $admin->is_active ? 'text-danger' : 'text-primary' }} hover:underline">
                                        {{ $admin->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $admins->links() }}
        </div>
    @endif
@endsection
