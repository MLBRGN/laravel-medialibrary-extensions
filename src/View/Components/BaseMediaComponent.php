<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;
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

    protected int $maxMediaCount = 1;// don't use in views directly, use $getConfig('maxMediaCount') instead'

    public ResolvedModel $resolvedModel;

    public function __construct(
        ?string $id = null,
        // New preferred API: modelReference (camelCase => model-reference in Blade)
        public mixed $modelReference = null,
        // Backward compatibility: modelOrClassName still accepted
        public mixed $modelOrClassName = null,
        public ?string $dataSource = 'default'
    )
    {
        parent::__construct($id);

        $this->mediaService = app(MediaService::class);

        // Normalize: prefer the new prop when provided; keep both in sync
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        // Resolve using the first non-null reference
        $reference = $this->modelOrClassName ?? $this->modelReference;
        $this->resolveModel($reference, $dataSource);
    }

    protected function resolveModel(mixed $modelOrClassName, ?string $dataSource = 'default'): void
    {
        // If no model reference provided, default to temporary-upload mode with
        // an empty ResolvedModel to keep views operational (some demo/test
        // blades render managers without binding a model reference).
        if ($modelOrClassName === null) {
            $this->resolvedModel = new ResolvedModel(
                model: null,
                modelType: '',
                modelId: null,
                temporaryUploadMode: true,
            );
        } else {
            $this->resolvedModel = $this->mediaService->resolveModelOrClassName(
                $modelOrClassName,
                $dataSource
            );
        }

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
