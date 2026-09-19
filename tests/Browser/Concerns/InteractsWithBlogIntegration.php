<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Browser\Concerns;

use Pest\Browser\Api\AwaitableWebpage;
use Pest\Browser\Api\PendingAwaitablePage;

trait InteractsWithBlogIntegration
{
    /**
     * Fill out the basic blog information.
     */
    protected function fillBlogForm(AwaitableWebpage|PendingAwaitablePage $page, string $title, string $content): AwaitableWebpage|PendingAwaitablePage
    {
        $page->type('#title', $title);

        // Check if TinyMCE is initialized for #content
        $isTinyMce = $page->script("typeof tinymce !== 'undefined' && tinymce.get('content') !== null");

        if ($isTinyMce) {
            $page->script("tinymce.get('content').setContent(" . json_encode($content) . ");");
        } else {
            $page->type('#content', $content);
        }

        return $page;
    }

    /**
     * Upload an image to the featured image manager (Single).
     */
    protected function uploadFeaturedImage(AwaitableWebpage|PendingAwaitablePage $page, string $fixture): string
    {
        $container = '[data-base-id="blog-main-inside"]';
        $input = "{$container} [data-mle-media-input]";
        $button = "{$container} [data-mle-media-upload-button]";

        $this->waitForMLE($page);
        $this->scrollIntoView($page, $container);
        
        $filename = basename($fixture);
        
        $page->attach($input, $fixture)
            ->click($button);

        // If not using XHR, wait for the page to reload
        $xhr = $page->script("document.querySelector('[data-mle-xhr-form]') !== null");
        if (!$xhr) {
            $this->waitForMLE($page);
        }

        $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

        return $filename;
    }

    /**
     * Upload an image to the gallery manager (Multiple).
     */
    protected function uploadToGallery(AwaitableWebpage|PendingAwaitablePage $page, string $fixture): string
    {
        $container = '[data-base-id="blog-gallery-outside"]';
        $input = "{$container} [data-mle-media-input]";
        $button = "{$container} [data-mle-media-upload-button]";

        $this->waitForMLE($page);
        $this->scrollIntoView($page, $container);

        $filename = basename($fixture);

        $page->attach($input, $fixture)
            ->click($button);

        // If not using XHR, wait for the page to reload
        $xhr = $page->script("document.querySelector('[data-mle-xhr-form]') !== null");
        if (!$xhr) {
            $this->waitForMLE($page);
        }

        $page->assertSee(__('medialibrary-extensions::messages.upload_success'));

        return $filename;
    }

    /**
     * Assert that the featured image is visible on the show page.
     */
    protected function assertFeaturedImageVisible(AwaitableWebpage|PendingAwaitablePage $page, string $filename): AwaitableWebpage|PendingAwaitablePage
    {
        $container = '[data-base-id="blog-main-show"]';
        $image = "{$container} [data-mle-media-preview-image]";

        $page->assertPresent("{$container} [data-mle-media-preview-item]")
            ->assertMissing("{$container} [data-mle-media-preview-container]:nth-child(2)");

        $src = $page->page()->locator($image)->first()->getAttribute('src');
        $this->assertFilenameMatch($src, $filename);

        return $page;
    }

    /**
     * Assert that the gallery has exactly the expected number of items.
     */
    protected function assertGalleryCount(AwaitableWebpage|PendingAwaitablePage $page, int $count): AwaitableWebpage|PendingAwaitablePage
    {
        $container = '[data-base-id="blog-gallery-show"]';
        
        if ($count > 0) {
            $page->assertPresent("{$container} [data-mle-media-preview-container]:nth-child({$count}) [data-mle-media-preview-item]");
        }
        
        $next = $count + 1;
        $page->assertMissing("{$container} [data-mle-media-preview-container]:nth-child({$next})");

        return $page;
    }

    /**
     * Assert that the gallery contains the specified images.
     */
    protected function assertGalleryImagesVisible(AwaitableWebpage|PendingAwaitablePage $page, array $filenames): AwaitableWebpage|PendingAwaitablePage
    {
        $container = '[data-base-id="blog-gallery-show"]';
        
        $this->assertGalleryCount($page, count($filenames));

        foreach ($filenames as $index => $filename) {
            $pos = $index + 1;
            $src = $page->page()->locator("{$container} [data-mle-media-preview-container]:nth-child({$pos}) [data-mle-media-preview-image]")->first()->getAttribute('src');
            
            $found = false;
            foreach ($filenames as $name) {
                if ($this->checkFilenameMatch($src, $name)) {
                    $found = true;
                    break;
                }
            }
            expect($found)->toBeTrue("None of the expected images found in gallery at position $pos (src: $src)");
        }

        return $page;
    }

    /**
     * Assert that the carousel contains the specified number of items and images.
     */
    protected function assertCarouselVisible(AwaitableWebpage|PendingAwaitablePage $page, array $filenames): AwaitableWebpage|PendingAwaitablePage
    {
        $container = '[data-base-id="blog-carousel-show"]';
        $count = count($filenames);

        $page->assertPresent($container);
        
        if ($count > 0) {
            $page->assertPresent("{$container} [data-mle-carousel-item]:nth-child({$count})");
        }
        
        $next = $count + 1;
        $page->assertMissing("{$container} [data-mle-carousel-item]:nth-child({$next})");

        $carouselSrcs = [];
        for ($i = 1; $i <= $count; $i++) {
            $carouselSrcs[] = $page->page()->locator("{$container} [data-mle-carousel-item]:nth-child({$i}) [data-mle-media-preview-image]")->first()->getAttribute('src');
        }

        foreach ($filenames as $name) {
            $found = false;
            foreach ($carouselSrcs as $src) {
                if ($this->checkFilenameMatch($src, $name)) {
                    $found = true;
                    break;
                }
            }
            expect($found)->toBeTrue("Image $name not found in carousel");
        }

        return $page;
    }

    /**
     * Set the item at the given position as the first one.
     */
    protected function setAsFirst(AwaitableWebpage|PendingAwaitablePage $page, string $containerSelector, int $position): AwaitableWebpage|PendingAwaitablePage
    {
        $item = "{$containerSelector} [data-mle-media-preview-container]:nth-child({$position})";
        $btn = "{$item} [data-mle-media-set-as-first-button]";

        $page->click($btn)
            ->assertSee(__('medialibrary-extensions::messages.medium_set_as_main'));

        return $page;
    }

    protected function assertFilenameMatch(string $src, string $expectedFilename): void
    {
        expect($this->checkFilenameMatch($src, $expectedFilename))
            ->toBeTrue("Expected source '$src' to match filename '$expectedFilename' (allowing underscore/hyphen variations)");
    }

    protected function checkFilenameMatch(string $src, string $expectedFilename): bool
    {
        $base = pathinfo($expectedFilename, PATHINFO_FILENAME);
        $ext = pathinfo($expectedFilename, PATHINFO_EXTENSION);

        // Match either underscore or hyphen in the base name
        $pattern = strtr(preg_quote($base), ['_' => '[_-]', '-' => '[_-]']) . '\.' . preg_quote($ext);

        return (bool) preg_match('/' . $pattern . '/', $src);
    }
}
