<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can create facility.
     */
    public function test_admin_can_create_facility(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/facilities', [
                'category_id' => $category->id,
                'name' => 'Gedung Rektorat',
                'description' => 'Kantor pusat administrasi',
                'latitude' => -6.1234,
                'longitude' => 106.1234,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('facilities', ['name' => 'Gedung Rektorat']);
    }

    /**
     * Test student cannot manage facilities.
     */
    public function test_student_cannot_manage_facilities(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::factory()->create();
        $facility = Facility::factory()->create(['category_id' => $category->id]);

        $this->actingAs($user)->postJson('/api/facilities', [])->assertStatus(403);
        $this->actingAs($user)->putJson("/api/facilities/{$facility->id}", [])->assertStatus(403);
        $this->actingAs($user)->deleteJson("/api/facilities/{$facility->id}")->assertStatus(403);
    }

    /**
     * Test deletion protection.
     */
    public function test_admin_cannot_delete_facility_with_reports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $facility = Facility::factory()->create();

        // Using the factory which now handles spatial location correctly
        Report::factory()->create([
            'facility_id' => $facility->id,
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/api/facilities/{$facility->id}");

        $response->assertStatus(422);
    }

    /**
     * Test facility index returns a lightweight summary payload.
     */
    public function test_facility_index_returns_lightweight_summary_payload(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Facility::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/facilities');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
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
            ]);

        $this->assertArrayNotHasKey('amenities', $response->json('data.0'));
    }

    /**
     * Test facility show returns detail without recursive nested building payload.
     */
    public function test_facility_show_returns_detail_without_recursive_nesting(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $facility = Facility::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/facilities/{$facility->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'coordinate' => ['lat', 'lng'],
                    'category',
                    'building',
                ],
            ]);

        $this->assertArrayNotHasKey('amenities', $response->json('data.building'));
    }
}
