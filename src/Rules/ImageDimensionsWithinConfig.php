<?php

namespace Mlbrgn\MediaLibraryExtensions\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Validates uploaded images against min/max width/height from config.
 * If the uploaded file is not an image (getimagesize fails), the rule passes.
 */
class ImageDimensionsWithinConfig implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return; // Not a file upload; nothing to validate here.
        }

        if (! $value->isValid()) {
            return; // Let other validators (e.g., 'file') handle invalid uploads.
        }

        $path = $value->getRealPath();
        if (! $path) {
            return; // Cannot resolve path; skip silently to avoid false negatives.
        }

        // Suppress warnings for non-image files; we'll treat them as pass-through.
        $info = @getimagesize($path);
        if ($info === false) {
            return; // Not an image; dimensions rule should not apply.
        }

        $width = $info[0] ?? null;
        $height = $info[1] ?? null;

        if ($width === null || $height === null) {
            return; // Unable to read dimensions; skip.
        }

        $maxW = (int) config('medialibrary-extensions.max_image_width', 7040);
        $maxH = (int) config('medialibrary-extensions.max_image_height', 3960);
        $minW = (int) config('medialibrary-extensions.min_image_width', 320);
        $minH = (int) config('medialibrary-extensions.min_image_height', 160);

        if ($width > $maxW || $height > $maxH) {
            $fail(trans('medialibrary-extensions::messages.image_too_large', [
                'max_width' => $maxW,
                'max_height' => $maxH,
                'width' => $width,
                'height' => $height,
            ]));
            return;
        }

        if ($width < $minW || $height < $minH) {
            $fail(trans('medialibrary-extensions::messages.image_too_small', [
                'min_width' => $minW,
                'min_height' => $minH,
                'width' => $width,
                'height' => $height,
            ]));
        }
    }
}
