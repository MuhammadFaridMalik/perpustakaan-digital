@extends('layouts.app')
@section('title', 'Booking Masuk')

@section('content')
    <p class="text-sm text-muted mb-5">Booking dari siswa yang menunggu diproses jadi peminjaman.</p>

    @if ($bookings->isEmpty())
        <div class="border border-dashed border-border rounded p-10 text-center text-muted text-sm">
            Tidak ada booking yang menunggu.
        </div>
    @else
        <div class="grid gap-3">
            @foreach ($bookings as $booking)
                <div class="border border-border rounded p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-medium">{{ $booking->book->title }}</p>
                        <p class="text-xs text-muted mt-0.5">
                            {{ $booking->siswa->name }} · {{ $booking->siswa->siswaProfile->nisn ?? '—' }}
                            · dibooking {{ $booking->booked_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex gap-4 shrink-0">
                        <form method="POST" action="{{ route('bookings.process', $booking) }}" onsubmit="return confirm('Proses booking ini jadi peminjaman?')">
                            @csrf
                            @method('PATCH')
                            <button class="text-sm text-primary hover:underline">Proses</button>
                        </form>
                        <form method="POST" action="{{ route('bookings.reject', $booking) }}" onsubmit="return confirm('Tolak booking ini?')">
                            @csrf
                            @method('PATCH')
                            <button class="text-sm text-danger hover:underline">Tolak</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @endif
@endsection
