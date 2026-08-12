<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;

class ResolvedModel
{
    public function __construct(
        public ?HasMediaExtended $model,
        public string $modelType,
        public ?int $modelId,
        public bool $temporaryUploadMode,
    ) {}
}
