<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;

class ResolvedModel
{
    public function __construct(
        public ?Model $model,
        public string $modelType,
        public ?int $modelId,
        public bool $temporaryUploadMode,
    ) {}
}
