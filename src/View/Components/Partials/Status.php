<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class Status extends BaseComponent
{
    public ?array $status = null;

    public function __construct(
        public string $id,
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

        // validation errors
        // always bound to media manager, don't use initiator_id for checking error bag
//        /** @var ViewErrorBag $errors */
//        $errors = session('errors');
//        if ($errors instanceof ViewErrorBag) {
//            $bagName = 'media_manager_'.$this->initiatorId;
//            $bag = $errors->getBag($bagName);
//
//            if ($bag->any()) {
//                $messages = $bag->all();
//                $this->status = [
//                    'initiator_id' => $this->initiatorId,
//                    'type' => 'error',
//                    'message' => implode("\n", $messages),
//                    'messages' => $messages,
//                ];
//            }
//        }
    }

    public function render(): View
    {
        return $this->getPartialView('status', $this->frontendTheme);
    }
}
