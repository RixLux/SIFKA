<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset')]
#[Description('Reset the database and clear all report images.')]
class ResetAppCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->call('migrate:fresh', ['--seed' => true]);
        $this->call('storage:clear-reports', ['--no-interaction' => true]);

        $this->info('Application reset completed successfully.');

        return self::SUCCESS;
    }
}
