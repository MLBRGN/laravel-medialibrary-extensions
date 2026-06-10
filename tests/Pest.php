<?php

use Mlbrgn\MediaLibraryExtensions\Tests\BrowserTestCase;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

uses(
    TestCase::class
)->in('Feature', 'Unit', 'Arch');

uses(
    BrowserTestCase::class
)->in('Browser');

pest()->browser()->headed();
// pest()->browser()->inFirefox();
// pest()->browser()->inSafari();
