<?php

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClearMediaLibraryCommand extends Command
{
    protected $signature = 'media-library-extensions:reset
                            {--force : Skip confirmation prompt}';

    protected $description = 'Completely clears Spatie Media Library data and files for all configured media disks.';

    public function handle(): int
    {
        // Ensure command only runs in safe environments
        if (!App::environment(['local', 'staging'])) {
            $this->error('This command can only be run in local or staging environments!');
            return self::FAILURE;
        }

        $this->warn('⚠️ This will permanently delete ALL media files and truncate the media table.');

        if (! $this->option('force') && ! $this->confirm('Do you really want to continue?')) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        // Media storage disks that need to be cleaned
        $disks = [
            'media',
            config('media-library-extensions.media_disks.demo'),
            config('media-library-extensions.media_disks.originals'),
            config('media-library-extensions.media_disks.temporary'),
        ];

        foreach ($disks as $disk) {
            if (config("filesystems.disks.$disk")) {
                $this->cleanDisk($disk);
            } else {
                $this->warn("Disk [$disk] not configured — skipping.");
            }
        }

        $this->truncateMediaTable();

        // Normal DB
        $this->truncateTemporaryUploads(config('database.default'));

        if (config('media-library-extensions.demo_pages_enabled')) {
            $demoConnection = config('media-library-extensions.demo_database_name');
            $this->truncateTemporaryUploads($demoConnection);
        }
        $this->info('Media library reset complete.');
        return self::SUCCESS;
    }

    protected function cleanDisk(string $disk): void
    {
        $this->line("Cleaning disk [$disk]...");

        try {
            $files = Storage::disk($disk)->allFiles();
            $dirs  = Storage::disk($disk)->allDirectories();

            Storage::disk($disk)->delete($files);
            foreach ($dirs as $dir) {
                Storage::disk($disk)->deleteDirectory($dir);
            }

            $this->info("Cleared all files and directories from [$disk].");
        } catch (\Throwable $e) {
            $this->error("Failed to clean disk [$disk]: " . $e->getMessage());
        }
    }

    protected function truncateMediaTable(): void
    {
        $this->line('Truncating media table...');

        try {
            DB::table('media')->truncate();
            $this->info('Media table truncated.');
        } catch (\Throwable $e) {
            $this->error('Failed to truncate media table: ' . $e->getMessage());
        }
    }

    protected function truncateTemporaryUploads(string $connectionName): void
    {
        $this->line("Clearing mle_temporary_uploads on connection [$connectionName]...");

        try {
            $connection = DB::connection($connectionName);
            $driver = $connection->getDriverName();

            if ($driver === 'sqlite') {
                $connection->table('mle_temporary_uploads')->delete();
            } else {
                $connection->table('mle_temporary_uploads')->truncate();
            }

            $this->info('Cleared successfully.');
        } catch (\Throwable $e) {
            $this->error("Failed to clear on [$connectionName]: " . $e->getMessage());
        }
    }
}
