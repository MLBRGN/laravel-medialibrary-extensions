<?php

namespace Mlbrgn\MediaLibraryExtensions\Exceptions;

use Exception;

class InvalidModelTypeException extends Exception
{
    public static function for(string $modelClass): self
    {
        return new self(
            "Invalid model type: {$modelClass}"
        );
    }

    public static function missingInterface(string $modelClass): self
    {
        return new self(
            "Model {$modelClass} must implement Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended"
        );
    }

    public static function missingTrait(string $modelClass): self
    {
        return new self(
            "Model {$modelClass} must use trait Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended"
        );
    }
}
