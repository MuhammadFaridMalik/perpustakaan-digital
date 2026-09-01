@extends('layouts.app')
@section('title', 'Detail Anggota')

@section('content')
    <div class="max-w-lg">
        <div class="flex items-start justify-between gap-4 mb-1">
            <h2 class="text-xl font-semibold">{{ $member->name }}</h2>
            <span class="text-xs font-medium {{ $member->is_active ? 'text-primary' : 'text-danger' }} whitespace-nowrap">
                {{ $member->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
        <p class="text-muted text-sm mb-6">{{ $member->email ?? 'Email belum diisi' }}</p>

        <dl class="grid grid-cols-2 gap-y-3 text-sm mb-6 border border-border rounded p-4">
            <dt class="text-muted">NISN</dt>
            <dd>{{ $member->siswaProfile->nisn ?? '—' }}</dd>
            <dt class="text-muted">Kelas</dt>
            <dd>{{ $member->siswaProfile->kelas ?? '—' }}</dd>
            <dt class="text-muted">Jurusan</dt>
            <dd>{{ $member->siswaProfile->jurusan ?? '—' }}</dd>
            <dt class="text-muted">Angkatan</dt>
            <dd>{{ $member->siswaProfile->angkatan ?? '—' }}</dd>
        </dl>

        <div class="flex gap-3">
            <a href="{{ route('members.edit', $member) }}"
               class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                Ubah Data
            </a>
            <form method="POST" action="{{ route('members.toggle-active', $member) }}"
                  onsubmit="return confirm('{{ $member->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                @csrf
                @method('PATCH')
                <button class="border border-border text-sm font-medium px-4 py-2 rounded {{ $member->is_active ? 'text-danger' : 'text-primary' }}">
                    {{ $member->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
        </div>
    </div>
@endsection
