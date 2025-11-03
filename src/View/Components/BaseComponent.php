<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

abstract class BaseComponent extends Component
{
    use ViewHelpers;

    public array $config = [];

    public string $id;

    public function __construct(
        ?string $id = null,
    ) {
        $this->id = filled($id) ? $id : 'component-'.Str::uuid();
    }

    public function hasCollections(): bool
    {
        // Check all defined collection types
        return collect($this->collections)
            ->only(['image', 'document', 'video', 'audio', 'youtube'])
            ->filter(fn ($value) => filled($value))
            ->isNotEmpty();
    }

    public function getCollectionValue(string $key, mixed $default = null): mixed
    {
        $value = $this->collections[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public function hasCollection(string $key): bool
    {
        return filled($this->collections[$key] ?? null);
    }

    public function resolveComponentForMedium($medium): ?string
    {
        $map = config('media-library-extensions.component_map', []);
        $type = getMediaType($medium); // your own helper or custom property
        return $map[$type] ?? null;
    }

}
