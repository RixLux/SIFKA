<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = [
            [
                'name' => 'Gedung A (Rektorat)',
                'description' => 'Gedung pusat administrasi dan rektorat',
                'lat' => -0.8979,
                'lng' => 100.3506,
            ],
            [
                'name' => 'Gedung B (Fakultas Teknik)',
                'description' => 'Gedung perkuliahan dan laboratorium teknik',
                'lat' => -0.8985,
                'lng' => 100.3515,
            ],
            [
                'name' => 'Gedung C (Perpustakaan)',
                'description' => 'Gedung perpustakaan pusat dan ruang baca',
                'lat' => -0.8992,
                'lng' => 100.3522,
            ],
        ];

        foreach ($buildings as $b) {
            Building::create([
                'name' => $b['name'],
                'description' => $b['description'],
                'location' => DB::raw("ST_GeomFromText('POINT({$b['lng']} {$b['lat']})', 4326)"),
            ]);
        }
    }
}
