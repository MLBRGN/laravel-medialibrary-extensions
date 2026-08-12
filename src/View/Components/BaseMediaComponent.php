<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;

abstract class BaseMediaComponent extends BaseComponent
{
    public mixed $modelReference = null;

    public ?Model $model = null;

    public ?string $modelType = null;

    public ?int $modelId = null;

    public bool $temporaryUploadMode = false;

    public MediaModelResolver $mediaModelResolver;

    public int $totalMediaCount = 0;

    protected int $maxMediaCount = 1; // don't use in views directly, use $getConfig('maxMediaCount') instead'

    public ResolvedModel $resolvedModel;

    /**
     * @var Application|mixed|MediaModelResolver|object
     */
    public function __construct(
        ?string $id,
        mixed $modelReference,
        public ?string $dataSource = 'default'
    ) {
        parent::__construct($id);

        //        $this->mediaService = app(MediaService::class);
        $this->mediaModelResolver = app(MediaModelResolver::class);

        $this->modelReference = $modelReference;

        $this->resolveModel($modelReference, $dataSource);
    }

    protected function resolveModel(mixed $modelReference, ?string $dataSource = 'default'): void
    {

        $this->resolvedModel = $this->mediaModelResolver->resolveModelReference(
            $modelReference,
            $dataSource
        );

        $this->setResolvedModelProperties($this->resolvedModel);
    }

    protected function setResolvedModelProperties(ResolvedModel $resolvedModel): void
    {
        $this->model = $resolvedModel->model;
        $this->modelType = $resolvedModel->modelType;
        $this->modelId = $resolvedModel->modelId;
        $this->temporaryUploadMode = $resolvedModel->temporaryUploadMode;
    }
}
