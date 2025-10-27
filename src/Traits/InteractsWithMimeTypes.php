<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

trait InteractsWithMimeTypes
{
    /**
     * Convert a comma-separated mimetype string to a normalized array.
     */
    protected function stringToMimeArray(string|array|null $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            return collect($input)
                ->map(fn ($mime) => trim($mime))
                ->filter()
                ->unique()
                ->values()
                ->toArray();
        }

        return collect(explode(',', $input))
            ->map(fn ($mime) => trim($mime))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Convert an array of mimetypes to a normalized comma-separated string.
     */
    protected function mimeArrayToString(array|string|null $input): string
    {
        if (empty($input)) {
            return '';
        }

        if (is_string($input)) {
            // normalize the string anyway
            return collect(explode(',', $input))
                ->map(fn ($mime) => trim($mime))
                ->filter()
                ->unique()
                ->join(', ');
        }

        return collect($input)
            ->map(fn ($mime) => trim($mime))
            ->filter()
            ->unique()
            ->join(', ');
    }

    /**
     * Returns an array of allowed mimetypes for the current collections.
     */
    protected function getAllowedMimeTypes(): array
    {
        // Option override
        if (! empty($this->getOption('allowedMimeTypes'))) {
            return $this->stringToMimeArray($this->getOption('allowedMimeTypes'));
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
     * Resolve both machine-readable and human-readable MIME types.
     */
    protected function resolveAllowedMimeTypes(): array
    {
        $mimes = $this->getAllowedMimeTypes();

        return [
            'allowedMimeTypes' => $this->mimeArrayToString($mimes),
            'allowedMimeTypesHuman' => $this->getAllowedMimeTypesHuman($mimes),
        ];
    }

    /**
     * Ensures that if `allowedMimeTypes` changes in options/config,
     * `allowedMimeTypesHuman` automatically updates too.
     */
    protected function syncAllowedMimeTypes(array &$config): void
    {
        //        dd($config);
        if (! isset($config['allowedMimeTypes'])) {
            //            $config['allowedMimeTypesHuman'] = '';
            return;
        }

        //        dd($config);
        // Convert to array first (handles strings or arrays)
        $mimes = $this->stringToMimeArray($config['allowedMimeTypes']);
        // dd($mimes);
        // Normalize both formats
        $config['allowedMimeTypes'] = $this->mimeArrayToString($mimes);
        $config['allowedMimeTypesHuman'] = $this->getAllowedMimeTypesHuman($mimes);

        //        dd($config);
    }
}
