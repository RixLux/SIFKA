<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can create category.
     */
    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->postJson('/api/categories', [
                'name' => 'Kelistrikan',
                'icon_marker' => 'bolt',
                'color_code' => '#FF0000',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Kelistrikan']);
    }

    /**
     * Test student cannot create category.
     */
    public function test_student_cannot_create_category(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)
            ->postJson('/api/categories', [
                'name' => 'Kelistrikan',
                'icon_marker' => 'bolt',
                'color_code' => '#FF0000',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test admin can update category.
     */
    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)
            ->putJson("/api/categories/{$category->id}", [
                'name' => 'New Name',
                'icon_marker' => 'star',
                'color_code' => '#00FF00',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('New Name', $category->fresh()->name);
    }

    /**
     * Test admin can delete category without facilities.
     */
    public function test_admin_can_delete_category_without_facilities(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test admin cannot delete category with facilities.
     */
    public function test_admin_cannot_delete_category_with_facilities(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();
        Facility::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)
            ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /**
     * Test all authenticated users can list categories.
     */
    public function test_all_users_can_list_categories(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        Category::factory()->count(3)->create();

        $response = $this->actingAs($user)
            ->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
