<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

// TODO implement
trait InteractsWithMediaModels
{
    /**
     * Whitelisted models that can be used with the media manager.
     */
    protected array $allowedMediaModels = [
        \App\Models\User::class,
        \App\Models\Post::class,
        // extend as needed
    ];

    /**
     * Resolve and authorize the model from the request.
     */
    protected function resolveMediaModel(Request $request): Model
    {
        $modelClass = $request->input('model_type');
        $modelId = $request->input('model_id');

        // 1. Validate model class
        abort_unless(
            in_array($modelClass, $this->allowedMediaModels, true),
            403,
            'Model not allowed'
        );

        // 2. Resolve model
        /** @var Model $model */
        $model = app($modelClass)::query()->findOrFail($modelId);

        // 3. Authorize
        $this->authorizeMediaAction($request, $model);

        return $model;
    }

    /**
     * Authorize the current user for the given model.
     */
    protected function authorizeMediaAction(Request $request, Model $model): void
    {
        $ability = $this->resolveAbility($request);

        abort_unless(
            $request->user()?->can($ability, $model),
            403,
            'Unauthorized action'
        );
    }

    /**
     * Determine which policy ability to use.
     */
    protected function resolveAbility(Request $request): string
    {
        // You can make this smarter if needed per route/action
        return match ($request->route()?->getName()) {
            'media.upload' => 'update',
            'media.delete' => 'update',
            'media.reorder' => 'update',
            default => 'update',
        };
    }

    /**
     * Validate and return the collection name.
     */
    protected function resolveMediaCollection(Request $request, Model $model): string
    {
        $collection = data_get($request->input('collections'), 'image');

        $allowed = $this->allowedCollectionsFor($model);

        abort_unless(
            in_array($collection, $allowed, true),
            403,
            'Invalid collection'
        );

        return $collection;
    }

    /**
     * Define allowed collections per model.
     */
    protected function allowedCollectionsFor(Model $model): array
    {
        return match (get_class($model)) {
            \App\Models\User::class => ['user-avatar'],
            \App\Models\Post::class => ['post-images'],
            default => [],
        };
    }

    /**
     * Scope media to the resolved model (prevents IDOR on media_id).
     */
    protected function resolveModelMedia(Model $model, Request $request)
    {
        return $model->media()->findOrFail(
            $request->input('media_id')
        );
    }
}
