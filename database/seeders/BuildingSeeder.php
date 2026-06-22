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
            ['name' => 'Gedung A (Rektorat)', 'description' => 'Gedung pusat administrasi dan rektorat', 'lat' => -0.8979, 'lng' => 100.3506],
            ['name' => 'Gedung B (Fakultas Teknik)', 'description' => 'Gedung perkuliahan dan laboratorium teknik', 'lat' => -0.8985, 'lng' => 100.3515],
            ['name' => 'Gedung C (Perpustakaan)', 'description' => 'Gedung perpustakaan pusat dan ruang baca', 'lat' => -0.8992, 'lng' => 100.3522],
            ['name' => 'Gedung D (Fakultas Ekonomi)', 'description' => 'Pusat studi manajemen dan akuntansi', 'lat' => -0.9000, 'lng' => 100.3530],
            ['name' => 'Gedung E (Lab Komputer)', 'description' => 'Pusat komputasi dan IT kampus', 'lat' => -0.9010, 'lng' => 100.3540],
            ['name' => 'Gedung F (Aula Besar)', 'description' => 'Gedung serbaguna untuk acara besar', 'lat' => -0.9020, 'lng' => 100.3550],
            ['name' => 'Gedung G (Fakultas Kedokteran)', 'description' => 'Gedung medis dan penelitian kesehatan', 'lat' => -0.9030, 'lng' => 100.3560],
            ['name' => 'Gedung H (Kantor Kemahasiswaan)', 'description' => 'Pusat organisasi mahasiswa', 'lat' => -0.9040, 'lng' => 100.3570],
            ['name' => 'Gedung I (Kantor Alumni)', 'description' => 'Pusat komunikasi alumni', 'lat' => -0.9050, 'lng' => 100.3580],
            ['name' => 'Gedung J (Kantin Pusat)', 'description' => 'Pusat kuliner kampus', 'lat' => -0.9060, 'lng' => 100.3590],
            ['name' => 'Gedung K (Pusat Olahraga)', 'description' => 'Lapangan indoor dan gym', 'lat' => -0.9070, 'lng' => 100.3600],
            ['name' => 'Gedung L (Laboratorium Kimia)', 'description' => 'Fasilitas riset kimia', 'lat' => -0.9080, 'lng' => 100.3610],
            ['name' => 'Gedung M (Pusat Bahasa)', 'description' => 'Pusat pembelajaran bahasa asing', 'lat' => -0.9090, 'lng' => 100.3620],
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
