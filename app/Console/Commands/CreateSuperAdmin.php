<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-super-admin';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a super admin user from environment variables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = config('app.super_admin.name');
        $email = config('app.super_admin.email');
        $password = config('app.super_admin.password');

        if (! $email || ! $password) {
            $this->error('SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD must be set in .env');

            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->info("User with email {$email} already exists.");

            return 0;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
        ]);

        $this->info("Super Admin '{$name}' created successfully.");

        return 0;
    }
}
