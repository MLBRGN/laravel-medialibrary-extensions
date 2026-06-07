<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

abstract class BaseComponent extends Component
{
    use ViewHelpers;

    //    public array $config = [];

    public string $id;

    public function __construct(
        ?string $id = null,
    ) {
        $this->id = filled($id) ? $id : 'component-'.Str::uuid();
    }

    public function renderView(string $viewName, ?string $theme = null, bool $isPartial = false, ?string $customView = null, array $data = []): View
    {
        $debug = config('medialibrary-extensions.debug', false);

        if ($debug) {
            DebugManager::pushScope($this->id);
        }

        if ($customView) {
            $view = view($customView, $data);
        } else {
            $view = $isPartial
                ? $this->getPartialView($viewName, $theme)
                : $this->getView($viewName, $theme);
        }

        if ($debug) {
            DebugManager::popScope($this->id);
        }

        return $view;
    }

    public function hasCollections(): bool
    {
        // Check all defined collection types
        return collect($this->collections)
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
        $map = config('medialibrary-extensions.component_map', []);
        $type = getMediaType($medium); // your own helper or custom property

        return $map[$type] ?? null;
    }
}
