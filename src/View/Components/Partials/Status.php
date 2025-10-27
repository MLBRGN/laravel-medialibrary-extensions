<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Status extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?array $status = null;

    public function __construct(
        ?string $id,
        public string $initiatorId,
        public string $mediaManagerId,
        public array $options = [],
    ) {
        parent::__construct($id);

        $statusKey = status_session_prefix(); // always one global key

        // own status messages stored in session
        if (session()->has($statusKey)) {
            $sessionStatus = session($statusKey);

            // Only attach if the initiator matches this component
            if (($sessionStatus['media_manager_id'] ?? null) === $this->mediaManagerId) {
                $this->status = $sessionStatus;
            }
        }
        $this->initializeConfig();
    }

    public function render(): View
    {
        return $this->getPartialView('status', $this->getConfig('frontendTheme'));
    }
}
