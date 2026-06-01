<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'facility_id' => Facility::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'image_path' => null,
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'resolved', 'rejected']),
            'location' => DB::raw("ST_GeomFromText('POINT(".$this->faker->longitude().' '.$this->faker->latitude().")', 4326)"),
        ];
    }
}
