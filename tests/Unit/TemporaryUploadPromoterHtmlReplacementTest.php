<?php

declare(strict_types=1);

namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit;

use Mlbrgn\MediaLibraryExtensions\Services\TemporaryUploadPromoter;

/**
 * We expose the protected replacement method for unit testing via a tiny test subclass.
 */
class TemporaryUploadPromoterTestProxy extends TemporaryUploadPromoter
{
    public function publicReplaceTemporaryUrlsInHtml(string $html, string $temporaryDiskUrl, string $mediaUrl, string $filename): string
    {
        // call the protected method from the parent
        return $this->replaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);
    }
}

it('replaces absolute temporary URLs with a single absolute media URL (no double host)', function (): void {
    $proxy = new TemporaryUploadPromoterTestProxy;

    $temporaryDiskUrl = 'http://127.0.0.1:8000';
    $mediaUrl = 'http://127.0.0.1:8000/storage/20/foto-op-04-12-2025-om-2321.jpg';
    $filename = 'foto-op-04-12-2025-om-2321.jpg';

    $html = <<<HTML
        <p>Some text before</p>
        <img src="http://127.0.0.1:8000/storage/media_temporary/abc123/{$filename}" alt="x" />
        <p>Some text after</p>
    HTML;

    $result = $proxy->publicReplaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);

    expect($result)->toContain($mediaUrl)
        ->and($result)->not->toContain('http://127.0.0.1:8000http://127.0.0.1:8000');
});

it('replaces relative temporary URLs too', function (): void {
    $proxy = new TemporaryUploadPromoterTestProxy;

    $temporaryDiskUrl = 'http://localhost'; // value is ignored for relative match branch
    $mediaUrl = '/storage/42/picture.png';
    $filename = 'picture.png';

    $html = '<img src="/storage/media_temporary/tmp/xyz/picture.png" />';

    $result = $proxy->publicReplaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);

    expect($result)->toContain($mediaUrl)
        ->and($result)->not->toContain('/storage/media_temporary/');
});

it('only replaces occurrences that end with the target filename', function (): void {
    $proxy = new TemporaryUploadPromoterTestProxy;

    $temporaryDiskUrl = 'http://test';
    $mediaUrl = '/storage/99/final.jpg';
    $filename = 'final.jpg';

    $html = '<img src="/storage/media_temporary/a/final.jpg" />'.
            '<img src="/storage/media_temporary/a/other.jpg" />';

    $result = $proxy->publicReplaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);

    // first image replaced, second remains temporary because filename differs
    expect($result)->toContain('<img src="'.$mediaUrl.'" />')
        ->and($result)->toContain('/storage/media_temporary/a/other.jpg');
});

it('consumes protocol-relative or mismatched hosts to avoid double-host after replacement', function (): void {
    $proxy = new TemporaryUploadPromoterTestProxy;

    // Temporary disk URL base is unknown/mismatched compared to the HTML host
    $temporaryDiskUrl = 'http://irrelevant-host';
    $mediaUrl = 'http://127.0.0.1:8000/storage/21/dino.png';
    $filename = 'dino.png';

    $cases = [
        '<img src="//127.0.0.1:8000/storage/media_temporary/xyz/dino.png" />',
        '<img src="http://127.0.0.1:8000/storage/media_temporary/xyz/dino.png" />',
    ];

    foreach ($cases as $html) {
        $result = $proxy->publicReplaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);

        expect($result)->toContain($mediaUrl)
            ->and($result)->not->toContain('http://127.0.0.1:8000http://127.0.0.1:8000');
    }
});

it('replaces urls inside srcset without creating double hosts', function (): void {
    $proxy = new TemporaryUploadPromoterTestProxy;

    $temporaryDiskUrl = '';
    $mediaUrl = 'http://127.0.0.1:8000/storage/30/photo.jpg';
    $filename = 'photo.jpg';

    $html = '<img srcset="/storage/media_temporary/a/photo.jpg 1x, http://127.0.0.1:8000/storage/media_temporary/a/photo.jpg 2x" />';

    $result = $proxy->publicReplaceTemporaryUrlsInHtml($html, $temporaryDiskUrl, $mediaUrl, $filename);

    expect($result)->not->toContain('media_temporary')
        ->and($result)->toContain($mediaUrl)
        ->and($result)->not->toContain('http://127.0.0.1:8000http://127.0.0.1:8000');
});
