<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('token:set-system-expiry {--d|day=0} {--m|minute=0} {--s|second=0}')]
#[Description('Set the system-wide token expiration in .env')]
class SetSystemExpiryCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('day');
        $minutes = (int) $this->option('minute');
        $seconds = (int) $this->option('second');

        // Total minutes calculation
        $totalMinutes = ($days * 1440) + $minutes + (int) ($seconds / 60);

        $envPath = base_path('.env');
        $envContent = File::get($envPath);

        if (str_contains($envContent, 'SANCTUM_EXPIRATION=')) {
            $envContent = preg_replace('/SANCTUM_EXPIRATION=.*/', 'SANCTUM_EXPIRATION='.$totalMinutes, $envContent);
        } else {
            $envContent .= "\nSANCTUM_EXPIRATION=".$totalMinutes."\n";
        }

        File::put($envPath, $envContent);

        $this->info("System token expiration set to {$totalMinutes} minutes (approx: {$days}d {$minutes}m {$seconds}s).");
        $this->warn("Please run 'php artisan config:clear' to apply changes.");

        return self::SUCCESS;
    }
}
