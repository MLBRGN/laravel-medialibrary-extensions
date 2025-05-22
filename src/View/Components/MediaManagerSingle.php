<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * The "MediaManagerSingle" class is responsible for handling the rendering and management
 * of single media items associated with a specific model. This includes displaying,
 * uploading, and optionally destroying media items.
 *
 * Properties:
 * - `theme`, `classes`: General properties that define the visual or functional aspects of the component.
 * - `modelKebabName`: A kebab-case representation of the associated model's name.
 * - `media`: A collection of associated media files.
 * - `medium`: The specific media item associated with the model and collection.
 * - `allowedMimeTypes`: A comma-separated string of the allowed MIME types for media uploads.
 *
 * Constructor Parameters:
 * - `model`: The associated model to fetch data from or manipulate.
 * - `mediaCollectionName`: The name of the media collection, if specified.
 * - `uploadEnabled`: A boolean indicating whether media uploads are enabled.
 * - `uploadRoute`: The route or endpoint for handling media uploads.
 * - `uploadFieldName`: The name of the file input field for uploads.
 * - `destroyEnabled`: A boolean to signify if media destruction is enabled.
 * - `destroyRoute`: The route or endpoint for media destruction.
 * - `showMediaUrl`: A boolean to determine if the media URL should be displayed.
 * - `modalId`: The identifier for the modal used in the component.
 * - `title`: The title of the media component, which may be used for display purposes.
 *
 * Functions:
 * - The constructor initializes the properties and sets up the component based on the
 *   provided parameters, including routes and allowed MIME types.
 * - The `render` function returns the view associated with the media manager component.
 *
 * !!!!! NOTE: while developing, remember to clean laravel cache after changes, otherwise cached views are used !!!!!
 * Clear the cache in the main application where the components are used by running php artisan optimize:clear
 * and run composer dump-autoload
 */
class MediaManagerSingle extends BaseComponent
{
    public $theme;

    public $classes;

    public string $modelKebabName = '';

    /** @var Collection<int, Media> */
    public Collection $media;

    public ?Media $medium = null;

    public string $allowedMimeTypes = '';

    public function __construct(
        public ?Model $model = null,
        public ?string $mediaCollectionName = null,
        public bool $uploadEnabled = false,
        public ?string $uploadRoute = null,
        public string $uploadFieldName = 'medium',
        public bool $destroyEnabled = false,
        public ?string $destroyRoute = null,
        public bool $showMediaUrl = false,
        public string $modalId = 'media-manager-single-modal',
        public string $title = '',
    ) {
        parent::__construct($model, $mediaCollectionName);

        // get medium only ever working with one medium
        $medium = $this->medium = $model->getFirstMedia($mediaCollectionName);

        // set routes
        $this->uploadRoute = $this->uploadRoute ?? route(mle_prefix_route('media-upload-single'));

        // an empty action attribute may cause the parent form to submit, check for empty route
        if ($medium) {
            $this->destroyRoute = ! empty($this->destroyRoute) ? $this->destroyRoute : route(mle_prefix_route('medium-destroy'), $medium->id);
        }

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-manager-single');
    }
}
