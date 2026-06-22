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
     * Test building index returns GeoJSON when requested.
     */
    public function test_building_index_returns_geojson_format(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Building::factory()->count(2)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/buildings?format=geojson');

        $response->assertStatus(200)
            ->assertJson([
                'type' => 'FeatureCollection',
            ])
            ->assertJsonStructure([
                'features' => [
                    '*' => [
                        'type',
                        'geometry' => ['type', 'coordinates'],
                        'properties' => ['id', 'name', 'type'],
                    ],
                ],
            ]);

        $this->assertEquals('Feature', $response->json('features.0.type'));
        $this->assertEquals('Point', $response->json('features.0.geometry.type'));
        $this->assertIsArray($response->json('features.0.geometry.coordinates'));
        $this->assertCount(2, $response->json('features.0.geometry.coordinates'));
    }

    /**
     * Test building index returns paginated data.
     */
    public function test_building_index_is_paginated(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Building::factory()->count(20)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/buildings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $this->assertEquals(20, $response->json('meta.total'));
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * Test building index returns a lightweight summary payload.
     */
    public function test_building_index_returns_lightweight_summary_payload(): void
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
                        'id',
                        'name',
                        'description',
                        'coordinate' => ['lat', 'lng'],
                        'facilities_count',
                    ],
                ],
            ])
            ->assertJsonMissingPath('data.0.amenities');

        $this->assertArrayNotHasKey('amenities', $response->json('data.0'));
        $this->assertEquals(2, $response->json('data.0.facilities_count'));
    }

    /**
     * Test building show returns detailed facilities without recursive nesting.
     */
    public function test_building_show_returns_detail_without_recursive_nesting(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $building = Building::factory()->create();
        Facility::factory()->count(1)->create(['building_id' => $building->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/buildings/{$building->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'coordinate' => ['lat', 'lng'],
                    'amenities' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'coordinate' => ['lat', 'lng'],
                            'category_id',
                            'building_id',
                            'category',
                            'building',
                        ],
                    ],
                ],
            ]);

        $this->assertNull($response->json('data.amenities.0.building'));
    }
}
