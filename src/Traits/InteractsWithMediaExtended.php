<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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
    //    public array $htmlEditorFields = [];

    /** Used by Spatie when registering conversions on the instance */
    public bool $registerMediaConversionsUsingModelInstance = true;

    // ============================================================
    // Archived Original URL Helpers
    // ============================================================

    protected array $conversionAspectRatios = [

        '16x10' => 16 / 10,
        '16x9' => 16 / 9,
        '5x3' => 5 / 3,
        '4x3' => 4 / 3,
        '3x2' => 3 / 2,

        '10x16' => 10 / 16,
        '9x16' => 9 / 16,
        '3x5' => 3 / 5,
        '3x4' => 3 / 4,
        '2x3' => 2 / 3,

        '1x1' => 1,
    ];

    // ============================================================
    // Boot logic for temporary uploads (unchanged)
    // ============================================================

    public static function bootInteractsWithMediaExtended(): void
    {

        static::created(function ($model) {
            if (! $model->exists || ! $model->getKey()) {
                Log::info('model with model type: '.$model->getMorphClass().' and id: '.$model->getKey().' does not exist');

                return;
            }

            app(TemporaryUploadPromoter::class)->promoteAllForModel($model);
        });
    }

    public function getArchivedOriginalUrlAttribute(): ?string
    {
        $path = $this->id.'/'.$this->file_name;

        return Storage::disk(config('media-library-extensions.media_disks.originals'))->exists($path)
            ? Storage::disk(config('media-library-extensions.media_disks.originals'))->url($path)
            : null;
    }

    public function getArchivedOriginalUrlFor(Media $media): ?string
    {
        $path = $media->id.'/'.$media->file_name;

        return Storage::disk(config('media-library-extensions.media_disks.originals'))->exists($path)
            ? Storage::disk(config('media-library-extensions.media_disks.originals'))->url($path)
            : null;
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

    // TODO look if needed
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
        $basePath = $media->getPath();

        if (! file_exists($basePath)) {
            return;
        }

        // Get the original image dimensions
        [$originalWidth, $originalHeight] = getimagesize($basePath);

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

        $return = $conversionCollection
            ->map(fn ($conversion) => $conversion->getName())
            ->unique()
            ->values()
            ->toArray();

        return $return;
    }

    public function getRequiredMediaAspectRatio(Media $medium): array
    {
        $conversions = $this->getConversionsForMedium($medium); // e.g. ["16x9","4x3"]
        $fallback = ['16:9' => round(16 / 9, 3)]; // default fallback ratio

        foreach ($conversions as $name) {
            // If known in predefined conversionAspectRatios map
            if (isset($this->conversionAspectRatios[$name])) {
                return [$name => $this->conversionAspectRatios[$name]];
            }

            // 2Try parsing "WxH" or "W:H" format dynamically
            if (preg_match('/^(\d+)[x:](\d+)$/', $name, $matches)) {
                $w = (int) $matches[1];
                $h = (int) $matches[2];
                if ($w > 0 && $h > 0) {
                    return [$name => round($w / $h, 3)];
                }
            }
        }

        // Fallback if nothing matched
        return $fallback;
    }

    public function getRequiredMediaAspectRatioString(Media $medium): string
    {
        $aspectRatio = $this->getRequiredMediaAspectRatio($medium);

        // Get the first key if any, or fallback to '4:3'
        $key = array_key_first($aspectRatio) ?? '4:3';

        // Replace "x" with ":" for consistency (e.g., 16x9 → 16:9)
        return str_replace('x', ':', $key);
    }

    public function getOriginalImageInfo(Media $media): array
    {
        $originalPath = $media->getCustomProperty('original_path');

        if (! $originalPath) {
            return $this->emptyImageInfo();
        }

        $originalExists = Storage::disk(config('media-library-extensions.media_disks.originals'))->exists($originalPath);

        if (! $originalExists) {
            return $this->emptyImageInfo();
        }

        return $this->getImageInfo($originalPath, config('media-library-extensions.media_disks.originals'));
    }

    public function getBaseImageInfo(Media $media, ?array $requiredAspectRatio = null): array
    {
        $path = $media->getPath();

        if (! $path || ! file_exists($path)) {
            return $this->emptyImageInfo();
        }

        return $this->getImageInfo($path, null, 0.05, $requiredAspectRatio);
    }

    public function getImageInfo(string $path, ?string $disk = null, float $tolerance = 0.01, $requiredAspectRatio = null): array
    {
        try {
            if ($disk) {
                $absolutePath = Storage::disk($disk)->path($path);
            } else {
                $absolutePath = $path;
            }

            $image = Image::make($absolutePath);
        } catch (\Throwable $e) {
            return $this->emptyImageInfo();
        }

        $width = $image?->width() ?? null;
        $height = $image?->height() ?? null;

        if (! $width || ! $height) {
            return $this->emptyImageInfo();
        }

        $ratio = round($width / $height, 3);

        // Format variants
        $dimensions = "{$width} × {$height}";
        $fractionFormat = number_format($ratio, 2).':1';
        $xFormat = "{$width}x{$height}";
        $colonFormat = "{$width}:{$height}";
        $approxLabel = null;

        // Match approximate known aspect ratios
        foreach (config('media-library-extensions.available_aspect_ratios', []) as $availableAspectRatio) {
            $value = $availableAspectRatio['value'] ?? null;
            if ($value !== null && $value !== -1) {
                if ($ratio > $value - $tolerance && $ratio < $value + $tolerance) {
                    $approxLabel = $availableAspectRatio['label'] ?? null;
                    break;
                }
            }
        }

        $imageInfo = [
            'width' => $width,
            'height' => $height,
            'ratio' => $ratio,
            'dimensions' => $dimensions,
            'fraction' => $fractionFormat,
            'x_format' => $xFormat,
            'colon_format' => $colonFormat,
            'approx_label' => $approxLabel,
            'display' => $approxLabel
                ? "{$fractionFormat} ({$approxLabel})"
                : $fractionFormat,
            'filled' => true,
            'maxWidth' => config('media-library-extensions.max_image_width'),
            'maxHeight' => config('media-library-extensions.max_image_height'),
            'minWidth' => config('media-library-extensions.min_image_width'),
            'minHeight' => config('media-library-extensions.min_image_height'),
        ];

        $flags = $this->getImageValidationFlags($imageInfo, $requiredAspectRatio);

        return array_merge($imageInfo, $flags);
    }

    public function getImageValidationFlags(array $imageInfo, ?array $requiredAspectRatio = null): array
    {
        // Dimension checks
        $tooWide = $imageInfo['width'] > ($imageInfo['maxWidth'] ?? PHP_INT_MAX);
        $tooTall = $imageInfo['height'] > ($imageInfo['maxHeight'] ?? PHP_INT_MAX);
        $tooNarrow = $imageInfo['width'] < ($imageInfo['minWidth'] ?? 0);
        $tooShort = $imageInfo['height'] < ($imageInfo['minHeight'] ?? 0);

        // Aspect ratio
        $requiredValue = null;
        $requiredLabel = __('media-library-extensions::messages.unknown');

        if (! empty($requiredAspectRatio)) {
            $requiredLabel = array_key_first($requiredAspectRatio);
            $requiredValue = $requiredAspectRatio[$requiredLabel];
        }

        $ratioOk = false;
        if (! empty($imageInfo['ratio']) && $requiredValue !== null) {
            $tolerance = 0.02; // ~2% tolerance
            $ratioOk = abs($imageInfo['ratio'] - $requiredValue) < $tolerance;
        }

        return [
            'tooWide' => $tooWide,
            'tooTall' => $tooTall,
            'tooNarrow' => $tooNarrow,
            'tooShort' => $tooShort,
            'ratioOk' => $ratioOk,
            'requiredLabel' => $requiredLabel,
            'requiredValue' => $requiredValue,
        ];
    }

    // Default placeholder structure for missing or invalid images.
    protected function emptyImageInfo(): array
    {
        return [
            'width' => null,
            'height' => null,
            'ratio' => null,
            'dimensions' => null,
            'fraction' => null,
            'x_format' => null,
            'colon_format' => null,
            'approx_label' => null,
            'display' => null,
            'filled' => false,
            'maxWidth' => config('media-library-extensions.max_image_width'),
            'maxHeight' => config('media-library-extensions.max_image_height'),
            'minWidth' => config('media-library-extensions.min_image_width'),
            'minHeight' => config('media-library-extensions.min_image_height'),
            'tooWide' => null,
            'tooTall' => null,
            'tooNarrow' => null,
            'tooShort' => null,
            'ratioOk' => null,
            'requiredLabel' => __('media-library-extensions::messages.unknown'),
            'requiredValue' => null,
        ];

    }

    public function getHtmlEditorFields(): array
    {
        return $this->htmlEditorFields ?? [];
    }
}
