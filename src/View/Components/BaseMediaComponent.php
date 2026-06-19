<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;
use Mlbrgn\MediaLibraryExtensions\Support\ClientContext;
use Illuminate\Database\Eloquent\Model;


abstract class BaseMediaComponent extends BaseComponent
{
    public ?Model $model = null;

    public ?string $modelType = null;

    public ?int $modelId = null;

    public bool $temporaryUploadMode = false;

    public string $clientToken;

    public MediaService $mediaService;

    public function __construct(?string $id = null)
    {
        parent::__construct($id);

        $this->mediaService = app(MediaService::class);

        $this->clientToken = app(ClientContext::class)->get();
    }

    // TODO better name
    protected function setModelProperties(ResolvedModel $resolvedModel): void
    {
        $this->model = $resolvedModel->model;
        $this->modelType = $resolvedModel->modelType;
        $this->modelId = $resolvedModel->modelId;
        $this->temporaryUploadMode = $resolvedModel->temporaryUploadMode;
    }

}
