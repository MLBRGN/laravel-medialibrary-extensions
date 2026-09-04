<?php

use Mlbrgn\MediaLibraryExtensions\Tests\BrowserTestCase;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

uses(
    TestCase::class
)->in('Feature', 'Unit', 'Arch');

uses(
    BrowserTestCase::class
)->group('browser')->in('Browser');

pest()->browser()->timeout(5000);

if (getenv('PEST_BROWSER_HEADED')) {
    pest()->browser()->headed();
}
