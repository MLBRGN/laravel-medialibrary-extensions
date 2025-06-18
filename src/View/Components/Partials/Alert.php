<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Alert extends BaseComponent
{
    public string $targetId;
    public ?array $status = null;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        string $targetId
    ) {
        parent::__construct($id, $frontendTheme);
        $this->targetId = $targetId;
        $statusKey = status_session_prefix();

        if (session()->has($statusKey)) {
            $status = session($statusKey);

            // Only set status if the target matches the component's targetId
            if (isset($status['target']) && $status['target'] === $this->targetId) {
                $this->status = $status;
            }

//            if ($theme === 'bootstrap-5') {
//                $this->extraClasses = 'w-100 alert ' . ($status['type'] === 'success' ? 'alert-success' : 'alert-danger');
//            }
        }
    }

    public function render(): View
    {
        return $this->getPartialView('alert', $this->theme);
    }
}
