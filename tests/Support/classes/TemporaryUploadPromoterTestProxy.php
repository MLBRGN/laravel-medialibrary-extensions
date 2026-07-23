<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\classes;

use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;

class TemporaryUploadPromoterTestProxy extends TemporaryUploadPromoter
{
    public function publicReplaceTemporaryUrlsInHtml(string $html, string $temporaryDiskUrl, string $temporaryFullUrl, string $mediaUrl, string $filename): string
    {
        // call the protected method from the parent
        return $this->replaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $temporaryFullUrl, $mediaUrl, $filename);
    }
}
