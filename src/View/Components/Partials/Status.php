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
        array $options = [],
    ) {
        parent::__construct($id);
        $this->options = $options;

        $statusKey = status_session_prefix(); // always one global key

        // own status messages stored in session
        if (session()->has($statusKey)) {
            $sessionStatus = session($statusKey);

            // Only attach if the initiator matches this component (base_id only)
            if (($sessionStatus['base_id'] ?? null) === $this->id) {
                $this->status = $sessionStatus;
            }
        }
        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'status';
    }

    public function render(): View
    {
        return $this->renderView('status', $this->getConfig('theme'), true);
    }
}
