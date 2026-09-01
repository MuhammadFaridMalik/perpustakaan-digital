<?php

namespace App\Support;

use App\Models\SystemSetting;

class LibrarySettings
{
    public static function borrowDurationDays(): int
    {
        return (int) (SystemSetting::where('key', 'durasi_pinjam_hari')->value('value') ?? 7);
    }

    public static function finePerDay(): int
    {
        return (int) (SystemSetting::where('key', 'denda_per_hari')->value('value') ?? 0);
    }

    public static function maxBooksPerStudent(): int
    {
        return (int) (SystemSetting::where('key', 'maks_buku_dipinjam')->value('value') ?? 2);
    }
}
