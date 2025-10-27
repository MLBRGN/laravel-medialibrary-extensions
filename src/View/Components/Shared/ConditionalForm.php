<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Shared;

use Illuminate\View\Component;
use Illuminate\View\View;

class ConditionalForm extends Component
{
    /**
     * Create the component instance.
     */
    public function __construct(
        public bool $useXhr = false,
        public array $formAttributes = [],
        public array $divAttributes = [],
        public string $method = 'post'
    ) {
        $this->method = strtolower($method);
    }

    public function render(): View|string
    {
        return view('media-library-extensions::components.shared.conditional-form');
    }

    public function requiresMethodSpoofing(): bool
    {
        return !in_array($this->method, ['get', 'post']);
    }
}
