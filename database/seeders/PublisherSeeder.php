<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = ['Gramedia Pustaka Utama', 'Bentang Pustaka'];

        foreach ($publishers as $name) {
            Publisher::firstOrCreate(['name' => $name]);
        }
    }
}
