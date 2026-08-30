<?php

namespace Database\Seeders;

use App\Models\SiswaProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@perpus.sch.id',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'admin.budi'],
            [
                'name' => 'Budi Admin',
                'email' => 'admin.budi@perpus.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $siswa = User::firstOrCreate(
            ['username' => '0051234567'],
            [
                'name' => 'Farid Siswa',
                'email' => null,
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'is_active' => true,
            ]
        );

        SiswaProfile::firstOrCreate(
            ['user_id' => $siswa->id],
            [
                'nisn' => '0051234567',
                'kelas' => 'XII RPL 1',
                'jurusan' => 'Rekayasa Perangkat Lunak',
                'angkatan' => '2023/2024',
            ]
        );
    }
}
