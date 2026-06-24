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

    public function __construct(
        ?string $id,
        public string $initiatorId,
        public ?string $mediaManagerDomId = null,
        array $options = [],
        public string $instanceId = '',
    ) {
        parent::__construct($id);

        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->id;

        // Ensure instanceId is derived from the mediaManagerDomId (the parent manager's identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerDomId);

        $this->options = $options;
        $this->resolveConfig();

    }

    public function render(): View
    {
        return $this->getPartialView('status-area', $this->getConfig('frontendTheme'));
    }
}
