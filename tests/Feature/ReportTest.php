<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Facility;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test user can create a report.
     */
    public function test_user_can_create_report(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::factory()->create();
        $facility = Facility::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/reports', [
                'facility_id' => $facility->id,
                'title' => 'Kerusakan Lampu',
                'description' => 'Lampu di lorong pecah',
                'image' => UploadedFile::fake()->image('report.jpg'),
                'latitude' => -6.12345678,
                'longitude' => 106.12345678,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', ['title' => 'Kerusakan Lampu']);

        $imageUrl = $response->json('data.image_url');
        $this->assertNotNull($imageUrl);
        $filename = basename($imageUrl);
        Storage::disk('public')->assertExists('reports/' . $filename);
    }

    /**
     * Test user can create pinpoint report without a facility.
     */
    public function test_user_can_create_pinpoint_report_without_facility(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)
            ->postJson('/api/reports', [
                'facility_id' => null,
                'title' => 'Pohon Tumbang',
                'description' => 'Ada pohon tumbang di jalan lingkar',
                'latitude' => -6.5555,
                'longitude' => 106.5555,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'title' => 'Pohon Tumbang',
            'facility_id' => null,
            'lat_report' => -6.5555,
        ]);
    }

    /**
     * Test staff can update report status.
     */
    public function test_staff_can_update_report_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::factory()->create();
        $facility = Facility::factory()->create(['category_id' => $category->id]);
        $report = Report::create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'title' => 'Bocor',
            'description' => 'Atap bocor',
            'status' => 'pending',
            'lat_report' => 0,
            'long_report' => 0,
        ]);

        $response = $this->actingAs($staff)
            ->patchJson("/api/reports/{$report->id}", [
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('in_progress', $report->fresh()->status);
    }

    /**
     * Test student cannot update report status.
     */
    public function test_student_cannot_update_report_status(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $category = Category::factory()->create();
        $facility = Facility::factory()->create(['category_id' => $category->id]);
        $report = Report::create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'title' => 'Bocor',
            'description' => 'Atap bocor',
            'status' => 'pending',
            'lat_report' => 0,
            'long_report' => 0,
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/reports/{$report->id}", [
                'status' => 'resolved',
            ]);

        $response->assertStatus(403);
    }
}
