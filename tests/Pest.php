<?php

use Mlbrgn\MediaLibraryExtensions\Tests\BrowserTestCase;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

uses(
    TestCase::class
)->in('Feature', 'Unit', 'Arch');

uses(
    BrowserTestCase::class
)->group('browser')->in('Browser');

pest()->browser()->timeout(5000);// @AI do not increase, 5 seconds should be enough!

if (getenv('PEST_BROWSER_HEADED')) {
    pest()->browser()->headed();
}
