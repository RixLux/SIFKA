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
        Report::factory()->create([
            'facility_id' => $facility->id,
            'user_id' => $admin->id,
            'lat_report' => 0,
            'long_report' => 0
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/api/facilities/{$facility->id}");

        $response->assertStatus(422);
    }
}
