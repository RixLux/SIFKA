<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class StorageMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate {from=public} {to=s3}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from one storage disk to another';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $from = $this->argument('from');
        $to = $this->argument('to');

        if ($from === $to) {
            $this->error('Source and destination disks must be different.');

            return 1;
        }

        try {
            $files = Storage::disk($from)->allFiles();
        } catch (\Exception $e) {
            $this->error("Could not list files on '{$from}' disk: ".$e->getMessage());

            return 1;
        }

        if (empty($files)) {
            $this->info("No files found on '{$from}' disk.");

            return 0;
        }

        $this->info('Found '.count($files)." files on '{$from}'. Starting migration to '{$to}'...");

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($files as $file) {
            try {
                $content = Storage::disk($from)->get($file);
                $visibility = Storage::disk($from)->getVisibility($file);

                Storage::disk($to)->put($file, $content, [
                    'visibility' => $visibility,
                ]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\nFailed to migrate '{$file}': ".$e->getMessage());
                $failCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info('Migration completed.');
        $this->info("Successfully migrated: {$successCount}");

        if ($failCount > 0) {
            $this->warn("Failed to migrate: {$failCount}");

            return 1;
        }

        return 0;
    }
}
