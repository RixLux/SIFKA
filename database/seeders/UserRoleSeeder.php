<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@sifka.go',
        ]);

        // Seed Staff
        User::factory()->staff()->create([
            'name' => 'Staff User 1',
            'email' => 'staff1@sifka.test',
        ]);

        User::factory()->staff()->create([
            'name' => 'Staff User 2',
            'email' => 'staff2@sifka.test',
        ]);

        // Seed Students
        User::factory()->student()->create([
            'name' => 'Student User',
            'email' => 'student@sifka.test',
        ]);

        // Seed some random users for each role
        User::factory(3)->staff()->create();
        User::factory(10)->student()->create();
    }
}
