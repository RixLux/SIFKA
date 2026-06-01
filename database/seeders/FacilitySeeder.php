<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Category;
use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildings = Building::all();
        $categories = Category::all();

        if ($buildings->isEmpty() || $categories->isEmpty()) {
            return;
        }

        foreach ($buildings as $building) {
            // Create 3 facilities for each building
            for ($i = 1; $i <= 3; $i++) {
                $category = $categories->random();

                // Slightly vary coordinates from building center
                $lat = -0.8979 + ($building->id * 0.0005) + ($i * 0.0001);
                $lng = 100.3506 + ($building->id * 0.0005) + ($i * 0.0001);

                Facility::create([
                    'building_id' => $building->id,
                    'category_id' => $category->id,
                    'name' => "Fasilitas {$i} di {$building->name}",
                    'description' => "Deskripsi fasilitas {$i} yang berada di dalam {$building->name}",
                    'location' => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"),
                ]);
            }
        }
    }
}
