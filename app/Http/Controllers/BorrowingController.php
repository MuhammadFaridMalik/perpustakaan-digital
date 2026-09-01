<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorrowingRequest;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Booking;
use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\Fine;
use App\Models\SiswaProfile;
use App\Support\LibrarySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $items = BorrowingItem::with(['borrowing.siswa', 'bookCopy.book'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($qq) use ($request) {
                    $qq->whereHas('borrowing.siswa', fn ($qs) => $qs->where('name', 'like', '%' . $request->search . '%'))
                       ->orWhereHas('bookCopy.book', fn ($qb) => $qb->where('title', 'like', '%' . $request->search . '%'));
                });
            })
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('borrowings.index', compact('items'));
    }

    public function create()
    {
        $books = Book::withCount(['copies as available_copies' => fn ($q) => $q->where('status', 'tersedia')])
            ->having('available_copies', '>', 0)
            ->orderBy('title')
            ->get();

        return view('borrowings.create', compact('books'));
    }

    public function store(StoreBorrowingRequest $request)
    {
        $validated = $request->validated();

        $siswaProfile = SiswaProfile::where('nisn', $validated['nisn'])->firstOrFail();
        $siswa = $siswaProfile->user;

        if (! $siswa->is_active) {
            return back()->with('error', 'Akun siswa ini nonaktif, tidak bisa melakukan peminjaman.')->withInput();
        }

        $bookIds = array_unique($validated['book_ids']);
        $max = LibrarySettings::maxBooksPerStudent();

        try {
            DB::transaction(function () use ($siswa, $bookIds, $max) {
                $activeCount = BorrowingItem::where('status', 'dipinjam')
                    ->whereHas('borrowing', fn ($q) => $q->where('siswa_id', $siswa->id))
                    ->count();

                $pendingBookingCount = Booking::where('siswa_id', $siswa->id)
                    ->whereIn('status', ['menunggu', 'siap_diambil'])
                    ->count();

                if (($activeCount + $pendingBookingCount + count($bookIds)) > $max) {
                    throw new \RuntimeException(
                        "Siswa ini hanya boleh meminjam maksimal {$max} buku bersamaan (termasuk booking aktif). " .
                        "Saat ini: {$activeCount} pinjaman aktif, {$pendingBookingCount} booking tertunda."
                    );
                }

                $borrowing = Borrowing::create([
                    'siswa_id' => $siswa->id,
                    'admin_id' => auth()->id(),
                    'borrowed_at' => now()->toDateString(),
                    'due_date' => now()->addDays(LibrarySettings::borrowDurationDays())->toDateString(),
                ]);

                foreach ($bookIds as $bookId) {
                    $copy = BookCopy::where('book_id', $bookId)
                        ->where('status', 'tersedia')
                        ->lockForUpdate()
                        ->orderBy('inventory_code')
                        ->first();

                    if (! $copy) {
                        $book = Book::find($bookId);
                        throw new \RuntimeException(
                            "Buku \"{$book?->title}\" ternyata sudah tidak ada eksemplar tersedia. Silakan ulangi transaksi."
                        );
                    }

                    $copy->status = 'dipinjam';
                    $copy->save();

                    BorrowingItem::create([
                        'borrowing_id' => $borrowing->id,
                        'book_copy_id' => $copy->id,
                        'status' => 'dipinjam',
                    ]);
                }

                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'checkout_borrowing',
                    'description' => 'Memproses peminjaman ' . count($bookIds) . " buku untuk siswa: {$siswa->name}",
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman berhasil diproses.');
    }

    public function returnItem(BorrowingItem $item)
    {
        if ($item->status !== 'dipinjam') {
            return back()->with('error', 'Item peminjaman ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($item) {
            $returnedAt = now()->toDateString();
            $dueDate = $item->borrowing->due_date->toDateString();
            $isLate = $returnedAt > $dueDate;

            $item->returned_at = $returnedAt;
            $item->status = $isLate ? 'telat' : 'dikembalikan';
            $item->received_by = auth()->id();
            $item->save();

            $copy = $item->bookCopy;
            $copy->status = 'tersedia';
            $copy->save();

            if ($isLate) {
                $daysLate = now()->diffInDays($item->borrowing->due_date);
                $amount = $daysLate * LibrarySettings::finePerDay();

                if ($amount > 0) {
                    Fine::create([
                        'borrowing_item_id' => $item->id,
                        'amount' => $amount,
                        'reason' => 'telat',
                        'status' => 'belum_dibayar',
                    ]);
                }
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'return_borrowing_item',
                'description' => "Memproses pengembalian eksemplar {$copy->inventory_code}" . ($isLate ? ' (terlambat)' : ''),
            ]);
        });

        return back()->with('success', 'Pengembalian berhasil diproses.');
    }
}
