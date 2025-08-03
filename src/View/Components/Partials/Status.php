<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Status extends BaseComponent
{
    public string $initiatorId;

    public ?array $status = null;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        string $initiatorId
    ) {
        parent::__construct($id, $frontendTheme);
        $this->initiatorId = $initiatorId;
        $statusKey = status_session_prefix();

        if (session()->has($statusKey)) {
            $status = session($statusKey);

            // Only set status if the target matches the component's initiatorId
            if (isset($status['initiator_id']) && $status['initiator_id'] === $this->initiatorId) {
                $this->status = $status;
            }
        }
    }

    public function render(): View
    {
        return $this->getPartialView('status', $this->theme);
    }
}
