<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Status extends BaseComponent
{
    public ?array $status = null;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        public string $initiatorId
    ) {
        parent::__construct($id, $frontendTheme);

        $statusKey = status_session_prefix(); // always one global key

        if (session()->has($statusKey)) {
            $sessionStatus = session($statusKey);

            // Only attach if the initiator matches this component
            if (($sessionStatus['initiator_id'] ?? null) === $this->initiatorId) {
                $this->status = $sessionStatus;
            }
//            else {
//                $this->status = null; // explicitly clear for non-matching
//            }
        }
    }

    public function render(): View
    {
        return $this->getPartialView('status', $this->frontendTheme);
    }
}
