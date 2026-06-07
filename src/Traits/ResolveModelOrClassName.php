<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use UnexpectedValueException;

trait ResolveModelOrClassName
{
    public ?Model $model = null;

    public ?string $modelType = null;

    public ?int $modelId = null;

    public bool $temporaryUploadMode = false;

    protected function resolveModelOrClassName(Model|string $modelOrClassName): void
    {
        if ($modelOrClassName instanceof HasMediaExtended) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();
            $this->temporaryUploadMode = false;

            //            dump($this->modelId);
        } elseif (is_string($modelOrClassName)) {
            if (! class_exists($modelOrClassName)) {
                throw new InvalidArgumentException(__('medialibrary-extensions::messages.class_not_found', [
                    'class' => $modelOrClassName,
                ]));
            }

            if (! is_subclass_of($modelOrClassName, HasMediaExtended::class)) {
                throw new UnexpectedValueException(__('medialibrary-extensions::messages.must_implement_has_media', [
                    'class' => $modelOrClassName,
                    'interface' => HasMediaExtended::class,
                ]));
            }

            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUploadMode = true;
        } else {
            throw new \TypeError('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }
    }
}
