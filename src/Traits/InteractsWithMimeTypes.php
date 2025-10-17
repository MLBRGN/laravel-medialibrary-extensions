<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Support\Collection;

trait InteractsWithMimeTypes
{
    /**
     * Returns an array of allowed mimetypes for the current collections.
     */
    protected function getAllowedMimeTypes(): array
    {
        // Option override
        if (!empty($this->getOption('allowedMimeTypes'))) {
            return collect(explode(',', $this->getOption('allowedMimeTypes')))
                ->map(fn ($mime) => trim($mime))
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }

        // Fallback: use configured mimetypes from collections
        $allowed = collect();

        foreach (['image', 'document', 'video', 'audio'] as $collection) {
            if (method_exists($this, 'hasCollection') && $this->hasCollection($collection)) {
                $allowed = $allowed->merge(
                    config("media-library-extensions.allowed_mimetypes.$collection", [])
                );
            }
        }

        return $allowed->flatten()->unique()->values()->toArray();
    }

    /**
     * Returns a human-readable list of allowed mimetypes (e.g. "JPEG, PNG, PDF").
     */
    protected function getAllowedMimeTypesHuman(array $allowedMimeTypes): string
    {
        return collect($allowedMimeTypes)
            ->map(fn ($mime) => mle_human_mimetype_label($mime))
            ->join(', ');
    }

    /**
     * Convenience method that returns both machine and human versions.
     */
    protected function resolveAllowedMimeTypes(): array
    {
        $mimes = $this->getAllowedMimeTypes();

        return [
            'allowedMimeTypes' => implode(',', $mimes),
            'allowedMimeTypesHuman' => $this->getAllowedMimeTypesHuman($mimes),
        ];
    }
}
