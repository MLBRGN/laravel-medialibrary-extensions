<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptions;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    use InteractsWithOptions;
    use ResolveModelOrClassName;

    public array $config;

    public bool $disableForm = false;

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using XHR

    public string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    public string $allowedMimeTypes = '';

    public bool $disabled = false;

    public bool $readonly = false;

    public bool $selectable = false;

    public bool $showDestroyButton = false;

    public bool $showMediaEditButton = false; // (at the moment) only for image editing

    public bool $showMenu = true;

    public bool $showOrder = false;

    public bool $showSetAsFirstButton = false;

    public bool $showUploadForm = true;

    public string $uploadFieldName = 'media';

    public ?bool $useXhr = true;

    public bool $temporaryUploads = false;

    protected array $optionKeys = [
        'allowedMimeTypes',
        'disabled',
        'readonly',
        'selectable',
        'showDestroyButton',
        'showMediaEditButton',
        'showMenu',
        'showOrder',
        'showSetAsFirstButton',
        'temporaryUploads',
        'useXhr',
        //        'frontendTheme',
    ];

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public bool $multiple = false,
    ) {
        $id = filled($id) ? $id : null;
        $frontendTheme = $this->options['frontendTheme'] ?? config('media-library-extensions.frontend_theme', 'bootstrap-5');
        $this->frontendTheme = $frontendTheme;

        parent::__construct($id, $frontendTheme);

        // apply matching options to class properties
        $this->mapOptionsToProperties($this->options);
        //        dump('this->id in mm after mapping'. $this->id);
        $collections = $this->mergeCollections($collections);

        $this->resolveModelOrClassName($modelOrClassName);

        // Override: enforce disabling "set-as-first" when multiple is disabled
        if (! $this->multiple) {
            $this->showSetAsFirstButton = false;
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->id .= '-mmm';
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
            $this->id .= '-mms';
        }

        //        dump('this->id after multiple: ' . $this->id);
        // Config array passed to view
        $this->config = [
            'id' => $this->id,
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'medium' => $this->medium,
            'collections' => $collections,
            'mediaUploadRoute' => $this->mediaUploadRoute,
            'previewUpdateRoute' => $this->previewUpdateRoute,
            'youtubeUploadRoute' => $this->youtubeUploadRoute,
            'csrfToken' => csrf_token(),
            'disabled' => $this->disabled,
            'frontendTheme' => $this->frontendTheme,
            'multiple' => $this->multiple,
            'options' => $this->options,
            'readonly' => $this->readonly,
            'selectable' => $this->selectable,
            'showDestroyButton' => $this->showDestroyButton,
            'showMediaEditButton' => $this->showMediaEditButton,
            'showMenu' => $this->showMenu,
            'showOrder' => $this->showOrder,
            'showSetAsFirstButton' => $this->showSetAsFirstButton,
            'showUploadForm' => $this->showUploadForm,
            'temporaryUploadMode' => $this->temporaryUploadMode ? 'true' : 'false',
            'useXhr' => $this->useXhr,
        ];
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->frontendTheme);
    }

    public function showRegularUploadForm(): bool
    {
        // Only check image, document, video, and audio types
        return collect($this->collections)
            ->only(['image', 'document', 'video', 'audio'])
            ->filter(fn ($value) => filled($value)) // ignore falsy (null, '', false)
            ->isNotEmpty();
    }

    public function hasCollections(): bool
    {
        // Check all defined collection types
        return collect($this->collections)
            ->only(['image', 'document', 'video', 'audio', 'youtube'])
            ->filter(fn ($value) => filled($value))
            ->isNotEmpty();
    }

    public function getCollectionValue(string $key, mixed $default = null): mixed
    {
        $value = $this->collections[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public function hasCollection(string $key): bool
    {
        return filled($this->collections[$key] ?? null);
    }

    public function mergeCollections($collections): array
    {

        // define default collection names
        return array_merge([
            'image' => '',
            'document' => '',
            'youtube' => '',
            'video' => '',
            'audio' => '',
        ], $collections);
    }

    public function mergeOptions($options): array
    {
        $defaults = [
            'allowedMimeTypes' => '',
            'disabled' => false,
            'frontendTheme' => null,
            'readonly' => false,
            'selectable' => false,
            'showDestroyButton' => false,
            'showMediaEditButton' => false,
            'showMenu' => true,
            'showOrder' => false,
            'showSetAsFirstButton' => false,
            'showUploadForm' => true,
            'temporaryUploads' => false,
            'uploadFieldName' => 'media',
            'useXhr' => true,
        ];

        return array_merge($defaults, $options);
    }
}
