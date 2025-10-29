<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;
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

    protected array $conversionAspectRatios = [

        '16x10' => 16 / 10,
        '16x9' => 16 / 9,
        '5x3' => 5/3,
        '4x3'  => 4 / 3,
        '3x2' => 3 / 2,

        '10x16' => 10 / 16,
        '9x16' => 9 / 16,
        '3x5' => 3/5,
        '3x4'  => 3 / 4,
        '2x3' => 2 / 3,

        '1x1'  => 1,
    ];

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
                Log::info('model with model type: ' . $model->getMorphClass() . ' and id: ' . $model->getKey() . ' does not exist');
                return;
            }

            app(TemporaryUploadPromoter::class)->promoteAllForModel($model);
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

    public function shouldStoreOriginals(): bool
    {
        // priority: model property → config value → default true
        return property_exists($this, 'storeOriginals')
            ? $this->storeOriginals
            : config('media-library-extensions.store_originals', true);
    }

    protected function addResponsiveAspectRatioConversion(
        Media $media,
        array $collections,
        float $aspectRatio,
        string $aspectRatioName,
        Fit $fit
    ): void {
        $originalPath = $media->getPath();

        if (!file_exists($originalPath)) {
            return;
        }

        // Get the original image dimensions
        [$originalWidth, $originalHeight] = getimagesize($originalPath);

        if ($aspectRatio <= 0) {
            return; // Invalid input
        }

        if (!$originalWidth || !$originalHeight) {
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

        $return = $conversionCollection
            ->map(fn($conversion) => $conversion->getName())
            ->unique()
            ->values()
            ->toArray();

        return $return;
    }

//    public function getMediaConversionsWithAspectRatio(Media $medium): array
//    {
//        $conversions = $this->getConversionsForMedium($medium); // ["16x9","4x3"]
//
//        $result = [];
//        foreach ($conversions as $name) {
//            if (str_contains($name, 'x')) {
//                [$w, $h] = explode('x', $name);
//                $result[$name] = (int) $w / (int) $h;
//            }
//        }
//
//        return $result;
//    }

    public function getMediaConversionsWithAspectRatio(Media $medium): array
    {
        $conversions = $this->getConversionsForMedium($medium); // ["16x9","4x3"]

        $result = [];
        foreach ($conversions as $name) {
            if (isset($this->conversionAspectRatios[$name])) {
                $result[$name] = $this->conversionAspectRatios[$name];
            } else {
                // fallback: try parsing the name if it looks like WxH
                if (str_contains($name, 'x')) {
                    [$w, $h] = explode('x', $name);
                    $w = (int) $w;
                    $h = (int) $h;
                    if ($w > 0 && $h > 0) {
                        $result[$name] = round($w / $h, 3);
                    }
                }
            }
        }
        return $result;
    }

    public function getImageInfo(Media $medium, float $tolerance = 0.05): ?array
    {
        $path = $medium->getPath();

        if (!file_exists($path)) {
            return null;
        }

        $image = Image::make($path);
        $info  = [
            'width' => $image->width(),
            'height' => $image->height(),
            'aspect_ratio' => round($image->width() / $image->height(), 3),
        ];

        if (!$info || empty($info['width']) || empty($info['height']) || $info['height'] == 0) {
            return null;
        }

        $width  = (int) $info['width'];
        $height = (int) $info['height'];
        $ratio  = $width / $height;

        // Format variants
        $dimensions        = "{$width} × {$height}";
        $fractionFormat    = number_format($ratio, 2) . ':1';
        $xFormat           = "{$width}x{$height}";
        $colonFormat       = "{$width}:{$height}";
        $approxLabel       = null;

        // Try to find a known aspect ratio match
        foreach (config('media-library-extensions.available_aspect_ratios', []) as $availableAspectRatio) {
            $value = $availableAspectRatio['value'] ?? null;
            if (!is_null($value) && $value !== -1) {
                if ($ratio > $value - $tolerance && $ratio < $value + $tolerance) {
                    $approxLabel = $availableAspectRatio['label'];
                    break;
                }
            }
        }

        return [
            'width'           => $width,
            'height'          => $height,
            'ratio'           => round($ratio, 3),
            'dimensions'      => $dimensions,      // e.g. "240 × 320"
            'fraction'        => $fractionFormat,  // e.g. "0.75:1"
            'x_format'        => $xFormat,         // e.g. "240x320"
            'colon_format'    => $colonFormat,     // e.g. "240:320"
            'approx_label'    => $approxLabel,     // e.g. "3:4" or "16:9"
            'display'         => $approxLabel
                ? "{$fractionFormat} ({$approxLabel})"
                : $fractionFormat, // e.g. "0.75:1 (3:4)"
        ];
    }

}
