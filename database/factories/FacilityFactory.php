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
        $attributes = [
            'building_id' => Building::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $attributes['location'] = DB::raw("ST_GeomFromText('POINT(".$this->faker->longitude().' '.$this->faker->latitude().")', 4326)");
        } else {
            $attributes['latitude'] = $this->faker->latitude();
            $attributes['longitude'] = $this->faker->longitude();
        }

        return $attributes;
    }
}
