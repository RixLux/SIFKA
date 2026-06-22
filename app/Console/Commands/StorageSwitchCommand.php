<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageSwitchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:switch {disk : The disk to switch to (e.g., local, public, s3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch the active storage disk in the .env file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $disk = $this->argument('disk');
        $validDisks = array_keys(config('filesystems.disks'));

        if (! in_array($disk, $validDisks)) {
            $this->error("Invalid disk '{$disk}'. Available disks: ".implode(', ', $validDisks));

            return 1;
        }

        $envPath = base_path('.env');

        if (! File::exists($envPath)) {
            $this->error('.env file not found.');

            return 1;
        }

        $content = File::get($envPath);

        // Update FILESYSTEM_DISK
        if (preg_match('/^FILESYSTEM_DISK=/m', $content)) {
            $content = preg_replace('/^FILESYSTEM_DISK=.*$/m', "FILESYSTEM_DISK={$disk}", $content);
        } else {
            $content .= "\nFILESYSTEM_DISK={$disk}";
        }

        // Update REPORT_DISK (specific to this app)
        if (preg_match('/^REPORT_DISK=/m', $content)) {
            $content = preg_replace('/^REPORT_DISK=.*$/m', "REPORT_DISK={$disk}", $content);
        } else {
            $content .= "\nREPORT_DISK={$disk}";
        }

        File::put($envPath, $content);

        $this->info("Successfully switched storage to '{$disk}' in .env");
        $this->warn("Note: Run 'php artisan config:clear' to apply changes if config is cached.");

        return 0;
    }
}
