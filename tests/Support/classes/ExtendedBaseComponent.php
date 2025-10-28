<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\classes;

// Concrete subclass for testing the abstract BaseComponent
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class ExtendedBaseComponent extends BaseComponent
{
    public function render()
    {
        return ''; // Dummy render method
    }
}
