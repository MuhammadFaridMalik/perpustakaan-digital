<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookCopy;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Support\LibrarySettings;
use Illuminate\Support\Facades\DB;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['siswa.siswaProfile', 'book'])
            ->where('status', 'menunggu')
            ->orderBy('booked_at')
            ->paginate(15);

        return view('bookings.index', compact('bookings'));
    }

    public function process(Booking $booking)
    {
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        if (! $booking->siswa->is_active) {
            return back()->with('error', 'Akun siswa ini nonaktif, booking tidak bisa diproses menjadi peminjaman.');
        }

        try {
            DB::transaction(function () use ($booking) {
                $copy = BookCopy::where('book_id', $booking->book_id)
                    ->where('status', 'tersedia')
                    ->lockForUpdate()
                    ->orderBy('inventory_code')
                    ->first();

                if (! $copy) {
                    throw new \RuntimeException('Tidak ada eksemplar tersedia saat ini. Tolak booking ini atau minta siswa menunggu.');
                }

                $borrowing = Borrowing::create([
                    'siswa_id' => $booking->siswa_id,
                    'admin_id' => auth()->id(),
                    'borrowed_at' => now()->toDateString(),
                    'due_date' => now()->addDays(LibrarySettings::borrowDurationDays())->toDateString(),
                ]);

                $copy->status = 'dipinjam';
                $copy->save();

                BorrowingItem::create([
                    'borrowing_id' => $borrowing->id,
                    'book_copy_id' => $copy->id,
                    'status' => 'dipinjam',
                ]);

                $booking->status = 'selesai';
                $booking->processed_by = auth()->id();
                $booking->save();

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'process_booking',
                    'description' => "Memproses booking menjadi peminjaman: {$booking->book->title} untuk {$booking->siswa->name}",
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil diproses menjadi peminjaman.');
    }

    public function reject(Booking $booking)
    {
        if ($booking->status !== 'menunggu') {
            return back()->with('error', 'Booking ini sudah diproses sebelumnya.');
        }

        $booking->status = 'dibatalkan';
        $booking->processed_by = auth()->id();
        $booking->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'reject_booking',
            'description' => "Menolak booking: {$booking->book->title} dari {$booking->siswa->name}",
        ]);

        return back()->with('success', 'Booking berhasil ditolak.');
    }
}
