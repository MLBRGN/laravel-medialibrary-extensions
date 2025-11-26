<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;

class RemoveExpiredTemporaryUploads extends Command
{
    protected $signature = 'media-library-extensions:remove-expired-temporary-uploads';

    protected $description = 'Deletes orphaned temporary uploads older than the session lifetime.';

    // short form aliases
    protected $aliases = [
        'mle:rm-expired-temporary-uploads',
        'mle:retu',
    ];

    public function handle(): int
    {
        $lifetimeMinutes = config('session.lifetime');
        $cutoff = now()->subMinutes($lifetimeMinutes);


        $query = TemporaryUpload::where('created_at', '<', $cutoff);

        $count = 0;
        $query->chunkById(100, function ($uploads) use (&$count) {
            foreach ($uploads as $upload) {
                Storage::disk($upload->disk)->delete($upload->path);
                $upload->delete();
                $count++;
            }
        });

        $this->info("Deleted {$count} temporary upload(s) older than {$lifetimeMinutes} minutes.");
        return self::SUCCESS;
    }

}
