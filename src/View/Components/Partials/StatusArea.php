<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class StatusArea extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    /** Identity of the parent MediaManager (logical ID, not suffixed) */
    public string $mediaManagerId;

    /** Identity of the parent MediaManager (DOM ID, potentially suffixed) */
    public string $mediaManagerDomId;

    public function __construct(
        ?string $id,
        public string $initiatorId,
        ?string $mediaManagerDomId = null,
        array $options = [],
        public string $instanceId = '',
        ?string $mediaManagerId = null,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->id;
        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->getDomId();

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's stable identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;
        $this->resolveConfig();

    }

    protected function domIdSuffix(): string
    {
        return 'status-area';
    }

    public function render(): View
    {
        return $this->getPartialView('status-area', $this->getConfig('frontendTheme'));
    }
}
