<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('seed:data {--a : Seed all data} {--user : Seed only users} {--no-user : Exclude users from seeding}')]
#[Description('Seed data with custom options: -a for all, --user for users only, --no-user to exclude users.')]
class SeedData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $all = $this->option('a');
        $userOnly = $this->option('user');
        $noUser = $this->option('no-user');

        if (! $all && ! $userOnly && ! $noUser) {
            $this->comment('Please use -a, --user, or --no-user. Use --help for more information.');

            return self::INVALID;
        }

        if ($all) {
            $this->info('Seeding all data...');
            $this->call('db:seed');
        } elseif ($userOnly) {
            $this->info('Seeding users only...');
            $this->call('db:seed', ['--class' => 'UserSeeder']);
        } elseif ($noUser) {
            $this->info('Seeding data (excluding users)...');
            // Assuming we run all seeders except UserSeeder
            // This is a common pattern for excluding specific seeders
            $this->call('db:seed', ['--class' => 'DatabaseSeeder', '--exclude' => 'UserSeeder']);
        }

        return self::SUCCESS;
    }
}
