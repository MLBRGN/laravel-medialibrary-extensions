<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

class CleanupTemporaryUploads extends Command
{
    protected $signature = 'media-library-extensions:clean-temporary-uploads';

    protected $description = 'Cleans / removes temporary uploads that are expired.';

    public function handle(): int
    {

        $expired = now()->subMinutes(120);

        $expiredUploads = TemporaryUpload::where('created_at', '<', $expired)->get();

        foreach ($expiredUploads as $upload) {
            Storage::disk($upload->disk)->delete($upload->path);
            $upload->delete();
        }

        $this->info("Cleaned up {$expiredUploads->count()} expired temporary uploads.");
        return self::SUCCESS;
    }

}
