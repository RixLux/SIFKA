<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create();

        $response = $this->actingAs($admin)->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(6, 'data'); // 1 admin + 5 users
    }

    public function test_student_cannot_list_users(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'new@example.com']);
    }

    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_can_create_staff_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'role' => 'staff',
        ]);
    }

    public function test_admin_can_update_user_role_to_staff(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
            'role' => 'staff',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'staff',
        ]);
    }
}
