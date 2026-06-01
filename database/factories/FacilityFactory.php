<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Category;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'location' => DB::raw("ST_GeomFromText('POINT(".$this->faker->longitude().' '.$this->faker->latitude().")', 4326)"),
        ];
    }
}
