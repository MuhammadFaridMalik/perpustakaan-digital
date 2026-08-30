<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = ['Pramoedya Ananta Toer', 'Andrea Hirata', 'Tere Liye'];

        foreach ($authors as $name) {
            Author::firstOrCreate(['name' => $name]);
        }
    }
}
