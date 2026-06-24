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

//    public string $clientToken;

    public MediaService $mediaService;

    public int $totalMediaCount = 0;

    public int $maxMediaCount = 1;

    public ResolvedModel $resolvedModel;

    public function __construct(
        ?string $id = null,
        mixed $modelOrClassName,
        public ?string $dataSource = 'default'
    )
    {
        parent::__construct($id);

        $this->mediaService = app(MediaService::class);

//        $this->clientToken = app(ClientContext::class)->get();

        $this->resolveModel($modelOrClassName, $dataSource);

//        dump('datasource '.$dataSource);
//        $this->totalMediaCount = $this->mediaService->countTemporaryUploadsInCollections(
//            $collections,
//            $instanceId,
//            $this->clientToken,
//            $this->dataSource
//        );
//        dump($this->totalMediaCount);
    }

    protected function resolveModel(mixed $modelOrClassName, ?string $dataSource = 'default'): void
    {
        $this->resolvedModel = $this->mediaService->resolveModelOrClassName(
            $modelOrClassName,
            $dataSource
        );

        $this->setResolvedModelProperties($this->resolvedModel);
    }

    // TODO better name
    protected function setResolvedModelProperties(ResolvedModel $resolvedModel): void
    {
        $this->model = $resolvedModel->model;
        $this->modelType = $resolvedModel->modelType;
        $this->modelId = $resolvedModel->modelId;
        $this->temporaryUploadMode = $resolvedModel->temporaryUploadMode;
    }

}
