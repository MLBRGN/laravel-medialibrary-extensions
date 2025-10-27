<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMediaExtended
{
    use InteractsWithMedia;

    /** Whether this model should store archived originals */
    protected bool $storeOriginals = true;

    /** Used by Spatie when registering conversions on the instance */
    public bool $registerMediaConversionsUsingModelInstance = true;

    // ============================================================
    // Archived Original URL Helpers
    // ============================================================

    public function getArchivedOriginalUrlAttribute(): ?string
    {
        $path = $this->id.'/'.$this->file_name;

        return Storage::disk('originals')->exists($path)
            ? Storage::disk('originals')->url($path)
            : null;
    }

    public function getArchivedOriginalUrlFor(Media $media): ?string
    {
        $path = $media->id.'/'.$media->file_name;

        return Storage::disk('originals')->exists($path)
            ? Storage::disk('originals')->url($path)
            : null;
    }

    // ============================================================
    // Boot logic for temporary uploads (unchanged)
    // ============================================================

    public static function bootInteractsWithMediaExtended(): void
    {
        static::created(function ($model) {
            if (!$model->exists || !$model->getKey()) {
                Log::info('model with model type: '.$model->getMorphClass().' and id: '.$model->getKey().' does not exist');
                return;
            }

            $temporaryUploads = TemporaryUpload::where('session_id', session()->getId())->get();
            $dirty = false;

            foreach ($temporaryUploads as $temporaryUpload) {
                $customProperties = collect($temporaryUpload->custom_properties)
                    ->except(['collections'])
                    ->toArray();

                $media = self::safeAddMedia(
                    $model,
                    $temporaryUpload->path,
                    $temporaryUpload->disk,
                    $temporaryUpload->getNameWithExtension(),
                    $temporaryUpload->collection_name,
                    $temporaryUpload->order_column,
                    $customProperties
                );

                // replace img urls from temporary to media in html editor fields
                if ($media && $tempUrl = $temporaryUpload->getUrl()) {
                    if (property_exists($model, 'htmlEditorFields')) {
                        foreach ($model->htmlEditorFields as $field) {
                            if (!empty($model->{$field})) {
                                $newValue = str_replace($tempUrl, $media->getUrl(), $model->{$field});
                                if ($newValue !== $model->{$field}) {
                                    $model->{$field} = $newValue;
                                    $dirty = true;
                                }
                            }
                        }
                    }
                }

                // remove the temporary file and record
                Storage::disk($temporaryUpload->disk)->delete($temporaryUpload->path);
                $temporaryUpload->delete();

                // copy original if enabled
//                if ($model->shouldStoreOriginals()) {
//                    $model->archiveOriginal($media);
//                }
            }

            // save once if anything was updated
            if ($dirty) {
                $model->saveQuietly();
            }
        });
    }

    // ============================================================
    // Helpers for safe media attach and archiving
    // ============================================================

    protected static function safeAddMedia(
        $model,
        $path,
        $disk,
        $filename,
        $collection,
        ?int $order = null,
        $customProperties = []
    ): ?Media {
        try {
            $media = $model
                ->addMediaFromDisk($path, $disk)
                ->preservingOriginal()
                ->withCustomProperties($customProperties)
                ->usingFileName($filename)
                ->toMediaCollection($collection);

            if ($order !== null) {
                $media->order_column = $order;
                $media->save();
            }

            return $media;
        } catch (Exception $e) {
            Log::error(__('media-library-extensions::messages.failed_to_attach_media', [
                'message' => $e->getMessage(),
            ]), [
                'path' => $path,
                'disk' => $disk,
                'filename' => $filename,
                'collection' => $collection,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

//    protected function archiveOriginal(Media $media): void
//    {
//        try {
//            $sourcePath = $media->getPath();
//            $destination = $media->id.'/'.$media->file_name;
//
//            if (!Storage::disk('originals')->exists($destination)) {
//                Storage::disk('originals')->put($destination, file_get_contents($sourcePath));
//            }
//        } catch (Exception $e) {
//            Log::error('Failed to archive original: '.$e->getMessage(), ['media_id' => $media->id]);
//        }
//    }

    public function shouldStoreOriginals(): bool
    {
        // priority: model property → config value → default true
        return property_exists($this, 'storeOriginals')
            ? $this->storeOriginals
            : config('media-library-extensions.store_originals', true);
    }

    protected function addResponsiveAspectRatioConversion(Media $media, array $collections, float $aspectRatio, string $aspectRatioName, Fit $fit): void
    {
        $originalPath = $media->getPath();

        if (! file_exists($originalPath)) {
            return;
        }

        // Get the original image dimensions
        [$originalWidth, $originalHeight] = getimagesize($originalPath);

        if ($aspectRatio <= 0) {
            return; // Invalid input
        }

        if (! $originalWidth || ! $originalHeight) {
            return; // getImageSize failed or empty image
        }

        // Calculate target width and height based on the aspect ratio
        $targetWidth = $originalWidth;
        $targetHeight = (int) round($originalWidth / $aspectRatio);

        // If the calculated height exceeds the original height, adjust it
        if ($targetHeight > $originalHeight) {
            $targetHeight = $originalHeight;
            $targetWidth = (int) round($originalHeight * $aspectRatio);
        }

        // Define the maximum allowed resolution (e.g., 1920x1080)
        $maxWidth = config('media-library-extensions.max_image_width', 1920);
        $maxHeight = config('media-library-extensions.max_image_height', 1080);

        // If the calculated width or height exceeds the max resolution, scale it down while maintaining the aspect ratio
        if ($targetWidth > $maxWidth || $targetHeight > $maxHeight) {
            // Calculate the scale ratio to fit within the max resolution
            $scale = min($maxWidth / $targetWidth, $maxHeight / $targetHeight);

            // Apply the scale to both width and height
            $targetWidth = (int) round($targetWidth * $scale);
            $targetHeight = (int) round($targetHeight * $scale);
        }

        if ($targetHeight === 0) {
            return;
        }

        // Add the conversion with the calculated dimensions
        $this
            ->addMediaConversion("$aspectRatioName")
            ->fit($fit, $targetWidth, $targetHeight) // Apply the resizing logic
            ->format('webp') // Optional: Convert to WebP format
            ->withResponsiveImages() // Enable responsive images
            ->performOnCollections(...$collections); // Apply to the given collections
    }

    protected function addResponsive16x9Conversion(Media $media, array $collections, Fit $fit = Fit::Crop): void
    {
        $this->addResponsiveAspectRatioConversion($media, $collections, 16 / 9, '16x9', $fit);
    }

    protected function addResponsive4x3Conversion(Media $media, array $collections, Fit $fit = Fit::Crop): void
    {
        $this->addResponsiveAspectRatioConversion($media, $collections, 4 / 3, '4x3', $fit);
    }

    protected function addResponsive1x1Conversion(Media $media, array $collections, Fit $fit = Fit::Crop): void
    {
        $this->addResponsiveAspectRatioConversion($media, $collections, 1, '1x1', $fit);
    }

    protected function addResponsive3x2Conversion(Media $media, array $collections, Fit $fit = Fit::Crop): void
    {
        $this->addResponsiveAspectRatioConversion($media, $collections, 3 / 2, '3x2', $fit);
    }

    protected function addYouTubeCollection($name): MediaCollection
    {
        return $this
            ->addMediaCollection($name);
    }

    // what conversions are defined for this media.
    // not the same as what conversions were actually generated.
    public function getConversionsForMedium(Media $medium): array
    {
        // Make sure conversions for this media are registered
        $this->registerMediaConversions($medium);

        $conversionCollection = collect($this->mediaConversions);

        return $conversionCollection
            ->map(fn ($conversion) => $conversion->getName())
            ->unique()
            ->values()
            ->toArray();
    }

    public function getMediaConversionsWithAspectRatio(Media $medium): array
    {
        $conversions = $this->getConversionsForMedium($medium); // ["16x9","4x3"]

        $result = [];
        foreach ($conversions as $name) {
            if (str_contains($name, 'x')) {
                [$w, $h] = explode('x', $name);
                $result[$name] = (int) $w / (int) $h;
            }
        }

        return $result;
    }
}
