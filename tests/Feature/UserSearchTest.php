<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_search_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);

        $response = $this->actingAs($admin)
            ->getJson('/api/users?query=John');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'John Doe']);
    }
}
