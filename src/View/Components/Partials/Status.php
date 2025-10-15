<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Status extends BaseComponent
{
    public ?array $status = null;

    public function __construct(
        ?string $id,
        public ?string $frontendTheme,
        public string $initiatorId,
        public string $mediaManagerId
    ) {
        parent::__construct($id, $frontendTheme);

        $statusKey = status_session_prefix(); // always one global key

        // own status messages stored in session
        if (session()->has($statusKey)) {
            $sessionStatus = session($statusKey);

            // Only attach if the initiator matches this component
            if (($sessionStatus['media_manager_id'] ?? null) === $this->mediaManagerId) {
                $this->status = $sessionStatus;
            }
        }
    }

    public function render(): View
    {
        return $this->getPartialView('status', $this->frontendTheme);
    }
}
