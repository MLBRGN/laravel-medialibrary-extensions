<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class TemporaryUploadSetAsFirstForm extends BaseComponent
{
    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';
    //    public ?string $imageCollection;
    //    public ?string $documentCollection;
    //    public ?string $youtubeCollection;
    //    public ?string $videoCollection;
    //    public ?string $audioCollection;

    public function __construct(
        ?string $id,
        public Collection $media,
        public TemporaryUpload $medium,
        public ?string $frontendTheme,
        public ?bool $useXhr = null,
        public array $collections = [], // in image, document, youtube, video, audio
        public bool $showSetAsFirstButton = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        // define default collection names
        $collections = array_merge([
            'image' => '',
            'document' => '',
            'youtube' => '',
            'video' => '',
            'audio' => '',
        ], $collections);

        $this->imageCollection = $collections['image'];
        $this->audioCollection = $collections['audio'];
        $this->videoCollection = $collections['video'];
        $this->documentCollection = $collections['document'];
        $this->youtubeCollection = $collections['youtube'];

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->targetMediaCollection = $medium->collection_name;
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

    }

    public function render(): View
    {

        return $this->getPartialView('temporary-upload-set-as-first-form', $this->frontendTheme);
    }
}
