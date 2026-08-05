<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;
use Illuminate\Database\Eloquent\Model;

abstract class BaseMediaComponent extends BaseComponent
{
    public ?Model $model = null;

    public ?string $modelType = null;

    public ?int $modelId = null;

    public bool $temporaryUploadMode = false;

    public MediaModelResolver $mediaModelResolver;

    public int $totalMediaCount = 0;

    protected int $maxMediaCount = 1;// don't use in views directly, use $getConfig('maxMediaCount') instead'

    public ResolvedModel $resolvedModel;
    /**
     * @var \Illuminate\Foundation\Application|mixed|MediaModelResolver|object
     */

    public function __construct(
        ?string $id = null,
        mixed $modelOrClassName,
        public ?string $dataSource = 'default'
    )
    {
        parent::__construct($id);

//        $this->mediaService = app(MediaService::class);
        $this->mediaModelResolver = app(MediaModelResolver::class);

        $this->resolveModel($modelOrClassName, $dataSource);
    }

    protected function resolveModel(mixed $modelOrClassName, ?string $dataSource = 'default'): void
    {
        $this->resolvedModel = $this->mediaModelResolver->resolveModelReference(
            $modelOrClassName,
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
