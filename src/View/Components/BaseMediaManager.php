<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

/**
 * Base class for components that ensures a model and its media collection are properly loaded.
 *
 * This class is designed to handle media-related operations for a given model. It validates the
 * presence of the required model and media collection name during instantiation and ensures
 * that the media relation is properly loaded before performing any actions.
 */
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
