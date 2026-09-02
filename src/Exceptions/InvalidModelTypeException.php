<?php

namespace Mlbrgn\MediaLibraryExtensions\Exceptions;

use Exception;

class InvalidModelTypeException extends Exception
{
    public static function for(string $modelClass): self
    {
        return new self(
            __('medialibrary-extensions::messages.class_does_not_exist', [
                'class_name' => $modelClass,
            ])
        );
    }

    public static function missingInterface(string $modelClass): self
    {
        return new self(
            __('medialibrary-extensions::messages.must_implement_has_media', [
                'class' => $modelClass,
                'interface' => 'Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended',
            ])
        );
    }

    public static function missingTrait(string $modelClass): self
    {
        return new self(
            "Model {$modelClass} must use trait Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended"
        );
    }
}
