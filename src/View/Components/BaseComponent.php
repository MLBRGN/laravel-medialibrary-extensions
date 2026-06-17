<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\DebugManager;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\ViewHelpers;

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

    public function __construct(
        ?string $id = null,
    ) {
        $this->originalId = filled($id) ? $id : (string) Str::ulid();
        $this->id = $this->originalId;
        $this->instanceId = InstanceManager::getInstanceId($this->originalId);

        $tokenFromCookie = request()->cookie('mle_client_token');

        // Capture client-side token (truly session-less)
        // We no longer fallback to session()->getId() to ensure persistent scoping
        $this->clientToken = request()->input('client_token')
            ?? $tokenFromCookie
            ?? (string) Str::ulid();

        // If it was just generated, try to set it for the remainder of the request
        if (! request()->has('client_token') && ! $tokenFromCookie) {
            request()->merge(['client_token' => $this->clientToken]);
        }
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
