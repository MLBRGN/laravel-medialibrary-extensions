<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Support\ClientContext;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

// only generic component functionality, that all component share
abstract class BaseComponent extends Component
{
    use ViewHelpers;

    /** @var string The unique DOM ID for this component instance */
    public string $id;

    /** @var string The stable, un-suffixed base ID (the logical identity) */
    public string $originalId;

    /** @var string The ULID used for scoping temporary uploads */
    public string $instanceId;

    /** @var string The stable logical identity of the client */
    public string $clientToken;

//    public MediaService $mediaService;

    public function __construct(
        ?string $id = null,
    ) {
        $this->originalId = filled($id) ? $id : (string) Str::ulid();
        $this->id = $this->originalId;
        $this->instanceId = InstanceManager::getInstanceId($this->originalId);

        $clientContext = new ClientContext(request());// todo remove here
        $this->clientToken = $clientContext->get();// todo remove here
    }

    public function setBaseId(string $id): void
    {
        $this->id = $id;
    }

    public function getSuffixedId(string $suffix): string
    {
        return $this->originalId.'-'.$suffix;
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

    // TODO move?
    public function hasCollections(): bool
    {
        // Check all defined collection types
        return collect($this->collections)
            ->filter(fn ($value) => filled($value))
            ->isNotEmpty();
    }

    // TODO move?
    public function getCollectionValue(string $key, mixed $default = null): mixed
    {
        $value = $this->collections[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    // TODO move?
    public function hasCollection(string $key): bool
    {
        return filled($this->collections[$key] ?? null);
    }

    // TODO move?
    public function resolveComponentForMedium($medium): ?string
    {
        $map = config('medialibrary-extensions.component_map', []);
        $type = getMediaType($medium); // your own helper or custom property

        return $map[$type] ?? null;
    }
}
