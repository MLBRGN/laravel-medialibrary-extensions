<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediumRestoreForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?string $mediaManagerId = '';

    public array $config;
    public string $mediumRestoreRoute;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedium = null,
        public array $collections = [],
        public array $options = [],
        public ?bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->mediaManagerId = $id;
        $this->id = $this->id.'-media-restore-form-'.$this->medium->id;

        if ($this->temporaryUploadMode) {
            throw new InvalidArgumentException(__('media-library-extensions::messages.temporary_upload_original_cannot_be_restored'));
        } else {
            $mediaRestoreRoute = route(
                mle_prefix_route('restore-original-medium'),
                ['media' => $medium->id]
            );
        }

        $this->mediumRestoreRoute = $mediaRestoreRoute;

        $this->initializeConfig();

        $this->setConfig('mediumDestroyRoute', $this->mediumRestoreRoute);
    }

    public function render(): View
    {
        return $this->getPartialView('medium-restore-form', $this->getConfig('frontendTheme'));
    }
}
