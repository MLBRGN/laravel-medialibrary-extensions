<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends MediaManager
{
    //    protected array $optionKeys = [
    //        'allowedMimeTypes',
    //        'disabled',
    //        'frontendTheme',
    //        //        'multiple', always false
    //        'readonly',
    //        'selectable',
    //        'showDestroyButton',
    //        'showMediaEditButton',
    //        'showMenu',
    //        //        'showOrder', always false
    //        'showSetAsFirstButton',
    //        'showUploadForm',
    //        'temporaryUploads',
    //        'uploadFieldName',
    //        'useXhr',
    //    ];

    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        array $collections = [],
        array $options = [],
    ) {
        // override options
        $options['showOrder'] = false;// should always be false

        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            medium: $medium,
            collections: $collections,
            options: $options,
            multiple: false,
        );

        // when medium provided, dont count collections
        if ($medium !== null) {
            $totalMediaCount = 1;
        } else {
            $totalMediaCount = 0;

            foreach ($collections as $collectionName) {
                if ($modelOrClassName instanceof HasMedia) {
                    $totalMediaCount += $modelOrClassName->getMedia($collectionName)->count();
                } elseif (is_string($modelOrClassName)) {
                    $totalMediaCount += TemporaryUpload::forCurrentSession($collectionName)->count();
                }
            }
        }

        // TODO implement disabled and readonly, this is not per se the same as disableForm

        // boolean property to disable form(s) in blade view(s)
        $this->setOption('disableForm', $totalMediaCount >= 1);

        $this->initializeConfig();
    }
}
