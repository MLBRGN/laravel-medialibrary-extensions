<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use InvalidArgumentException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Base class for components that ensures a model and its media collection are properly loaded.
 *
 * This class is designed to handle media-related operations for a given model. It validates the
 * presence of the required model and media collection name during instantiation and ensures
 * that the media relation is properly loaded before performing any actions.
 */
abstract class BaseComponent extends Component
{
    public string $theme;

    public array $classes;

    /** @var Collection<int, Media> */
    public Collection $media;

    public ?array $status = [];

    public function __construct(
        public ?Model $model,
        public ?string $mediaCollection,
        public string $id,
    ) {
        // This should now trigger if all is wired up properly
        $this->theme = config('media-library-extensions.frontend_theme', 'plain');
        $this->classes = config("media-library-extensions.classes.$this->theme", []);

        if (is_null($model)) {
            throw new InvalidArgumentException(
                __('media-library-extensions::messages.missing_model', ['component' => static::class])
            );
        }

        if (is_null($mediaCollection)) {
            throw new InvalidArgumentException(
                __('media-library-extensions::messages.missing_collection', ['component' => static::class])
            );
        }

        $this->model = $this->ensureMediaIsLoaded($model);

        // Then access the media
        $this->media = $this->model->getMedia($mediaCollection);

        $this->status = session(status_session_prefix());

        if (empty($this->id)) {
            $this->id = 'component-'.uniqid();
        }
        //        dd($this->status);
        //        $this->statusMessages = StatusFlash::pull();
        //        $this->media = $model->getMedia($mediaCollection);
        //        $this->modelKebabName = Str::kebab(class_basename($this->model));
    }

    protected function ensureMediaIsLoaded(Model $model): Model
    {

        return $model->relationLoaded('media')
            ? $model
            : $model->newQuery()->with('media')->findOrFail($model->getKey());

    }
}
