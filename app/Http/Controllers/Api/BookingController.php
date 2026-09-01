<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\Booking;
use App\Support\LibrarySettings;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('book')
            ->where('siswa_id', Auth::id())
            ->latest('booked_at')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'book_title' => $b->book->title,
                'status' => $b->status,
                'booked_at' => $b->booked_at->toDateTimeString(),
                'expires_at' => $b->expires_at?->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function store(StoreBookingRequest $request)
    {
        $siswa = Auth::user();
        $book = Book::findOrFail($request->book_id);

        $activeBorrowCount = \App\Models\BorrowingItem::where('status', 'dipinjam')
            ->whereHas('borrowing', fn ($q) => $q->where('siswa_id', $siswa->id))
            ->count();

        $pendingBookingCount = Booking::where('siswa_id', $siswa->id)
            ->whereIn('status', ['menunggu', 'siap_diambil'])
            ->count();

        $max = LibrarySettings::maxBooksPerStudent();

        if (($activeBorrowCount + $pendingBookingCount) >= $max) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah mencapai batas maksimal {$max} buku (pinjaman + booking aktif).",
            ], 422);
        }

        $alreadyBooked = Booking::where('siswa_id', $siswa->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['menunggu', 'siap_diambil'])
            ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki booking aktif untuk buku ini.',
            ], 422);
        }

        $hasAvailable = $book->copies()->where('status', 'tersedia')->exists();

        if (! $hasAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada eksemplar tersedia untuk buku ini saat ini.',
            ], 422);
        }

        $booking = Booking::create([
            'siswa_id' => $siswa->id,
            'book_id' => $book->id,
            'status' => 'menunggu',
            'booked_at' => now(),
            'expires_at' => now()->addDays(2),
        ]);

        AuditLog::create([
            'user_id' => $siswa->id,
            'action' => 'create_booking',
            'description' => "Booking buku: {$book->title}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat. Segera datang ke perpustakaan sebelum ' . $booking->expires_at->format('d M Y') . '.',
            'data' => $booking,
        ], 201);
    }

    public function destroy(Booking $booking)
    {
        if ($booking->siswa_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini sudah diproses dan tidak bisa dibatalkan.',
            ], 422);
        }

        $booking->status = 'dibatalkan';
        $booking->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'cancel_booking',
            'description' => "Membatalkan booking buku: {$booking->book->title}",
        ]);

        return response()->json(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }
}
