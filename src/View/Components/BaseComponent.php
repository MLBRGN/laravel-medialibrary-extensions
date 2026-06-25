<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\ClientContext;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

// only generic component functionality, that all the component share
abstract class BaseComponent extends Component
{
    use ViewHelpers;

    /** Logical component identity */
    public readonly string $id;// never to be modified

    /** HTML/DOM identity */
//    private string $domId;// todo must go

    /** identify the instance of a component (more than one can be on same page), also used for scoping temporary uploads, together with clientToken */
    public string $instanceId;

    /** Identity of the client, used for scoping temporary uploads, together with instanceId */
    public string $clientToken;

    public function __construct(
        ?string $id = null,
    ) {
        $this->id = filled($id) ? $id : (string) Str::ulid();
        $this->domId = $this->id;
        $this->instanceId = InstanceManager::getInstanceId($this->id);
        $this->clientToken = app(ClientContext::class)->get();
    }

    // can be overridden in child classes
    protected function domIdSuffix(): string
    {
        return '';
    }

    public function getDomId(): string
    {
        $suffix = $this->domIdSuffix();

        return $suffix === ''
            ? $this->id
            : "{$this->id}-{$suffix}";
    }

    public function renderView(string $viewName, ?string $theme = null, bool $isPartial = false, ?string $customView = null, array $data = []): View
    {
        $debug = config('medialibrary-extensions.debug', false);

        if ($debug) {
            DebugManager::pushScope($this->getDomId());
        }

        if ($customView) {
            $view = view($customView, $data);
        } else {
            $view = $isPartial
                ? $this->getPartialView($viewName, $theme)
                : $this->getView($viewName, $theme);
        }

        if ($debug) {
            DebugManager::popScope($this->getDomId());
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
