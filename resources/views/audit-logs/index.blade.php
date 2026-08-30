@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
    @if ($logs->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Belum ada aktivitas tercatat.
        </div>
    @else
        <div class="border border-border rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-bg border-b border-border text-left text-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Waktu</th>
                        <th class="px-4 py-3 font-medium">Pengguna</th>
                        <th class="px-4 py-3 font-medium">Aksi</th>
                        <th class="px-4 py-3 font-medium">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-muted whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="px-4 py-3"><code class="text-xs bg-bg px-1.5 py-0.5 rounded">{{ $log->action }}</code></td>
                            <td class="px-4 py-3">{{ $log->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
@endsection
