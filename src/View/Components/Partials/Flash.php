<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\Component;
use Illuminate\View\View;

class Flash extends Component
{
    public string $targetId;
    public ?array $status = null;
    public ?string $extraClasses = null;

    public function __construct(
        string $targetId
    ) {
        $this->targetId = $targetId;
        $statusKey = status_session_prefix();

        if (session()->has($statusKey)) {
            $status = session($statusKey);

            // Only set status if the target matches the component's targetId
            if (isset($status['target']) && $status['target'] === $this->targetId) {
                $this->status = $status;
            }

            if (config('media-library-extensions.frontend_theme') === 'bootstrap-5') {
                $this->extraClasses = 'w-100 alert ' . ($status['type'] === 'success' ? 'alert-success' : 'alert-danger');
            }
        }
    }

    public function render(): View
    {
        return view('media-library-extensions::components.partial.flash');
    }
}
