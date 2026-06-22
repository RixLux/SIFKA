<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TokenExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_expires_after_set_duration(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        // Generate a token that expires in 2 seconds
        $expiresAt = now()->addSeconds(2);
        $token = $user->createToken('test_token', ['*'], $expiresAt)->plainTextToken;

        // 1. Verify token works immediately
        $this->withToken($token)
            ->getJson('/api/user')
            ->assertStatus(200);

        // 2. Wait for 3 seconds to ensure expiration
        sleep(3);

        // 3. Verify token is unauthorized
        $this->withToken($token)
            ->getJson('/api/user')
            ->assertStatus(401);
    }
}
