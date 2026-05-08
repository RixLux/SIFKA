<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CreateSuperAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the create-super-admin command.
     */
    public function test_it_can_create_super_admin_from_config(): void
    {
        Config::set('app.super_admin.name', 'Super Admin');
        Config::set('app.super_admin.email', 'admin@example.com');
        Config::set('app.super_admin.password', 'secret123');

        $this->artisan('app:create-super-admin')
            ->expectsOutput("Super Admin 'Super Admin' created successfully.")
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
    }

    /**
     * Test the command fails if config is missing.
     */
    public function test_it_fails_if_config_is_missing(): void
    {
        Config::set('app.super_admin.email', null);

        $this->artisan('app:create-super-admin')
            ->expectsOutput('SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD must be set in .env')
            ->assertExitCode(1);
    }
}
