<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

abstract class BaseMediaManager extends BaseComponent
{
    public function __construct(
        public string $id,
        public ?string $frontendTheme = null
    ) {
        parent::__construct($id, $frontendTheme);
    }

    // prevent n+1 queries
    //    protected function ensureMediaIsLoaded(Model $model): Model
    //    {
    //
    //        return $model->relationLoaded('media')
    //            ? $model
    //            : $model->newQuery()->with('media')->findOrFail($model->getKey());
    //
    //    }
}
