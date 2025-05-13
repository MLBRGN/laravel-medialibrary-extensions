<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use InvalidArgumentException;

abstract class BaseComponent extends Component
{
    public $theme;

    public $classes;

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollectionName = null
    ) {
        // This should now trigger if all is wired up properly
        $this->theme = config('media-library-extensions.frontend-theme', 'plain');
        $this->classes = config("media-library-extensions.classes.{$this->theme}", []);

        if (is_null($model)) {
            throw new InvalidArgumentException(
                __('media-library-extensions::messages.missing_model', ['component' => static::class])
            );
        }

        if (is_null($mediaCollectionName)) {
            throw new InvalidArgumentException(
                __('media-library-extensions::messages.missing_collection', ['component' => static::class])
            );
        }

        $this->media = $model->getMedia($mediaCollectionName);
        $this->modelKebabName = Str::kebab(class_basename($model));
    }
}
