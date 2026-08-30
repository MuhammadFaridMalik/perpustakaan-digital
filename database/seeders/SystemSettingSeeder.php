<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'durasi_pinjam_hari', 'value' => '7', 'description' => 'Durasi peminjaman default (hari)'],
            ['key' => 'denda_per_hari', 'value' => '1000', 'description' => 'Denda keterlambatan per hari (Rupiah)'],
            ['key' => 'maks_buku_dipinjam', 'value' => '2', 'description' => 'Maksimal buku dipinjam bersamaan per siswa'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
