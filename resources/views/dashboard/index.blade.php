@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
        <div class="border border-border rounded p-5">
            <p class="text-sm text-muted">Total Admin</p>
            <p class="text-3xl font-semibold mt-1">{{ $totalAdmin }}</p>
        </div>
        <div class="border border-border rounded p-5">
            <p class="text-sm text-muted">Total Siswa Terdaftar</p>
            <p class="text-3xl font-semibold mt-1">{{ $totalSiswa }}</p>
        </div>
        <div class="border border-border rounded p-5 {{ $akunNonaktif > 0 ? 'border-accent/40' : '' }}">
            <p class="text-sm text-muted">Akun Nonaktif</p>
            <p class="text-3xl font-semibold mt-1">{{ $akunNonaktif }}</p>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-medium mb-3">Aktivitas Terbaru</h2>

        @if ($recentLogs->isEmpty())
            <p class="text-sm text-muted border border-dashed border-border rounded p-6 text-center">
                Belum ada aktivitas tercatat.
            </p>
        @else
            <div class="border border-border rounded divide-y divide-border">
                @foreach ($recentLogs as $log)
                    <div class="px-4 py-3 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm">{{ $log->description }}</p>
                            <p class="text-xs text-muted mt-0.5">{{ $log->user->name ?? 'Sistem' }}</p>
                        </div>
                        <span class="text-xs text-muted whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
