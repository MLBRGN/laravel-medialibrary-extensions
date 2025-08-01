<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TemporaryMediaService
{
    // TODO IMPLEMENT
    public function storeTemporary(UploadedFile $file, string $draftId): string
    {
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();
        $path = "tmp-media/$draftId/$filename";
        Storage::put($path, file_get_contents($file));
        return $filename;
    }

    // TODO IMPLEMENT
    public function moveToModel(string $draftId, Model $model, string $collection): void
    {
        $files = Storage::files("tmp-media/$draftId");

        foreach ($files as $file) {
            $model->addMedia(storage_path("app/$file"))
                ->preservingOriginal()
                ->toMediaCollection($collection);

            Storage::delete($file);
        }

        Storage::deleteDirectory("tmp-media/$draftId");
    }
}
