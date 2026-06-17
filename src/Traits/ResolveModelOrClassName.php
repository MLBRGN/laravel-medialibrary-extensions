<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

trait ResolveModelOrClassName
{
    public ?Model $model = null;

    public ?string $modelType = null;

    public ?int $modelId = null;

    public bool $temporaryUploadMode = false;

    protected function resolveModelOrClassName(Model|string $modelOrClassName, ?string $dataSource = 'default'): void
    {
        $resolved = app(MediaService::class)->resolveModelOrClassName($modelOrClassName, $dataSource);

        $this->model = $resolved->model;
        $this->modelType = $resolved->modelType;
        $this->modelId = $resolved->modelId;
        $this->temporaryUploadMode = $resolved->temporaryUploadMode;
    }
}
