<?php

namespace Mlbrgn\MediaLibraryExtensions\Data;

use Illuminate\Http\UploadedFile;

class PreparedUpload
{
    public function __construct(
        public UploadedFile $file,
        public string $collectionType,
        public string $collectionName,
        public array $collections,
        public string $originalName,
        public string $mimeType,
        public int $size,
    ) {}
}
