<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Building',
            'description' => $this->faker->sentence(),
            'location' => DB::raw("ST_GeomFromText('POINT(".$this->faker->longitude().' '.$this->faker->latitude().")', 4326)"),
        ];
    }
}
