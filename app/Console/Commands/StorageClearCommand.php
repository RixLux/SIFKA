<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('storage:clear-reports')]
#[Description('Clear all report images from the configured report disk.')]
class StorageClearCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $diskName = config('filesystems.report_disk');
        $disk = Storage::disk($diskName);

        if ($this->confirm("Are you sure you want to delete all report images from the [{$diskName}] disk?")) {
            $files = $disk->files('reports');
            $disk->delete($files);
            $this->info('Report images cleared successfully.');

            return self::SUCCESS;
        }

        return self::FAILURE;
    }
}
