<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewGrid extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        string $id,
        public mixed $modelReference,// either a modal that implements HasMediaExtended or it's class name
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
        ?string $instanceId = '___DEFAULT___',
        public ?string $dataSource = 'default',
        ?string $clientToken = '___DEFAULT___',
    ) {
        parent::__construct($id, $this->modelReference, $dataSource, null, $instanceId, $clientToken);

        $this->options = $options;

        $this->resolveConfig([
            'temporaryUploadMode' => $this->temporaryUploadMode,
        ]);
    }

    protected function domIdSuffix(): string
    {
        return 'media-preview-grid';
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-grid', $this->getConfig('theme'));
    }
}
