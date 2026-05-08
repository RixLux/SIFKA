<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can create building.
     */
    public function test_admin_can_create_building(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->postJson('/api/buildings', [
                'name' => 'Gedung Rektorat',
                'description' => 'Kantor Pusat',
                'latitude' => -6.1234,
                'longitude' => 106.1234,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('buildings', ['name' => 'Gedung Rektorat']);
    }

    /**
     * Test student cannot manage buildings.
     */
    public function test_student_cannot_manage_buildings(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $building = Building::factory()->create();

        $this->actingAs($user)->postJson('/api/buildings', [])->assertStatus(403);
        $this->actingAs($user)->putJson("/api/buildings/{$building->id}", [])->assertStatus(403);
        $this->actingAs($user)->deleteJson("/api/buildings/{$building->id}")->assertStatus(403);
    }

    /**
     * Test nested facilities in building index.
     */
    public function test_building_index_returns_nested_facilities(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $building = Building::factory()->create();
        Facility::factory()->count(2)->create(['building_id' => $building->id]);

        $response = $this->actingAs($user)
            ->getJson('/api/buildings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'name', 'coordinate', 'amenities'
                    ]
                ]
            ]);
    }
}
