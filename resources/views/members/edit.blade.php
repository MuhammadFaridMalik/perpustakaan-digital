@extends('layouts.app')
@section('title', 'Ubah Data Anggota')

@section('content')
    <div class="max-w-lg">
        @if ($errors->any())
            <div class="mb-4 rounded border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('members.update', $member) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name', $member->name) }}" required
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Email (opsional)</label>
                <input type="email" name="email" value="{{ old('email', $member->email) }}"
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn', $member->siswaProfile->nisn) }}" required
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                <p class="text-xs text-muted mt-1">Mengubah NISN juga akan memperbarui username login siswa.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Kelas</label>
                <input type="text" name="kelas" value="{{ old('kelas', $member->siswaProfile->kelas) }}" required
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jurusan</label>
                <input type="text" name="jurusan" value="{{ old('jurusan', $member->siswaProfile->jurusan) }}" required
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Angkatan</label>
                <input type="text" name="angkatan" value="{{ old('angkatan', $member->siswaProfile->angkatan) }}" required placeholder="Contoh: 2023/2024"
                       class="w-full rounded border border-border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-primary hover:bg-primary-hover text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                    Simpan Perubahan
                </button>
                <a href="{{ route('members.show', $member) }}" class="text-sm text-muted px-4 py-2">Batal</a>
            </div>
        </form>
    </div>
@endsection
