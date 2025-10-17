<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadForm extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;
    use InteractsWithMimeTypes;

    public ?string $mediaManagerId = '';
    public array $config = [];

    public function render(): View
    {
        return $this->getPartialView('upload-form', $this->frontendTheme);
    }

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName, // either a model implementing HasMedia or its class name
        public Media|TemporaryUpload|null $medium = null,
        public array $collections = [], // image, document, video, audio, etc.
        public array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        $this->mediaManagerId = $id;

        parent::__construct($id, $this->getOption('frontendTheme'));

        $this->resolveModelOrClassName($modelOrClassName);

        $mimeData = $this->resolveAllowedMimeTypes();

        $this->initializeConfig([
            'frontendTheme' => config('media-library-extensions.frontend_theme'),
            'useXhr' => config('media-library-extensions.use_xhr'),
            ...$mimeData,
        ]);

    }
}
