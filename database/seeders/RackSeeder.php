<?php

namespace Database\Seeders;

use App\Models\Rack;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        $racks = [
            ['code' => 'A1', 'description' => 'Rak fiksi, lantai 1'],
            ['code' => 'A2', 'description' => 'Rak non-fiksi, lantai 1'],
            ['code' => 'B1', 'description' => 'Rak sains & teknologi, lantai 2'],
        ];

        foreach ($racks as $rack) {
            Rack::firstOrCreate(['code' => $rack['code']], $rack);
        }
    }
}
