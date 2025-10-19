<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetAsFirstForm extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';
    public array $config;

    public function __construct(
        ?string $id,
        public Collection $media,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,// TODO should never be temporary upload, but then I get error on demo pages?
        public array $collections, // in image, document, youtube, video, audio
        public array $options = [],
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $this->getOption('frontendTheme'));

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-set-as-first-form-'.$this->medium->id;

        $this->targetMediaCollection = $medium->collection_name;

        $this->resolveModelOrClassName($modelOrClassName);

        $this->initializeConfig([
//            'showSetAsFirstButton' => $this->getOption('showSetAsFirstButton'),
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('set-as-first-form', $this->getConfig('frontendTheme'));
    }
}
