<?php

//
// Pest presets
//

use Illuminate\Support\Facades\Redirect;

arch()->preset()->php();

arch('Traits must be traits')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Traits')
    ->toBeTraits();

arch('Requests are classes, extend Command, have handle method and have suffix Request')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests')
    ->classes()
    ->toHaveSuffix('Request')
    ->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->toHaveMethod('rules');

arch('Commands are classes, extend Command, and have handle method')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Console\Commands')
    ->classes()
    ->toExtend('Illuminate\Console\Command')
    ->toHaveMethod('handle');

arch('Does not extend Console Command outside Mlbrgn\SpatieMediaLibraryExtensions\Console\Commands')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions')
    ->not->toExtend('Illuminate\Console\Command')
    ->ignoring('Mlbrgn\SpatieMediaLibraryExtensions\Console\Commands');

arch('Providers should have suffix ServiceProvider')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Providers')
    ->toExtend('Illuminate\Support\ServiceProvider')
    ->toHaveSuffix('ServiceProvider');

arch('Does not extend ServiceProvider outside App\Providers')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions')
    ->not->toExtend('Illuminate\Support\ServiceProvider')
    ->ignoring('Mlbrgn\SpatieMediaLibraryExtensions\Providers');

arch('classes do not have ServiceProvider suffix outside Providers')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions')
    ->not->toHaveSuffix('ServiceProvider')
    ->ignoring('Mlbrgn\SpatieMediaLibraryExtensions\Providers');

arch('classes do not have Controller suffix outside Controllers')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions')
    ->not->toHaveSuffix('Controller')
    ->ignoring('Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers');

arch('Controllers have suffix Controller')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('Policies classes have suffix Policy')
    ->expect('Mlbrgn\SpatieMediaLibraryExtensions\Policies')
    ->classes()
    ->toHaveSuffix('Policy');

//
// custom arch tests
//

arch('No debugging statements left in code')
    ->expect(['dd', 'ddd', 'dump', 'die', 'var_dump', 'sleep', 'print_r', 'echo', 'print', 'phpinfo', 'ray'])
    ->not->toBeUsed();

//arch('Seeders have Seeder suffix')
//    ->expect('Database\Seeders')
//    ->toHaveSuffix('Seeder');
//
//arch('Factories have Factory suffix')
//    ->expect('Database\Factories')
//    ->toHaveSuffix('Factory');

arch('Do not use env helper in code')
    ->expect(['env'])
    ->not->toBeUsed();

arch('Test files must have correct suffix')
    ->expect('Tests')
    ->toHaveSuffix('Test')
    ->ignoring([
        'Tests\Pest',
        'Tests\TestCase',
        'Tests\Database',
        'Tests\Models',
    ]);

arch('No direct database queries')
    ->expect(['DB::select', 'DB::insert', 'DB::update', 'DB::delete', 'DB::statement'])
    ->not->toBeUsed();

arch('No deprecated PHP functions used')
    ->expect(['create_function', 'split', 'mysql_query', 'ereg', 'each'])
    ->not->toBeUsed();

arch('does not contain debugging statements in views', function () {
    // Define the directory containing your views
    $viewDirectory = resource_path('views');

    // Define the debugging statements you want to search for
    $debuggingStatements = ['@dd', '@dump', '@var_dump', '@ray', '@sleep', '@exit', '@print_r'];

    // Recursively scan the directory for all .blade.php files
    $bladeFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($viewDirectory)
    );

    foreach ($bladeFiles as $file) {
        // Only check .blade.php files
        if ($file->isFile() && $file->getExtension() === 'php') {
            $fileContents = file_get_contents($file->getRealPath());

            foreach ($debuggingStatements as $statement) {
                expect($fileContents)->not->toContain($statement);
            }
        }
    }
});

arch('does not contain debugging statements in JavaScript files', function () {
    $jsDirectory = realpath(__DIR__ . '/../../resources/js');

    $debuggingStatements = ['debugger', 'alert('];

    $jsFiles = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($jsDirectory)
    );

    foreach ($jsFiles as $file) {
        if ($file->isFile() && $file->getExtension() === 'js') {
            $fileContents = file_get_contents($file->getRealPath());

            foreach ($debuggingStatements as $statement) {
                expect($fileContents)->not->toContain($statement);
            }
        }
    }
});

arch('Does not use the redirect facade for redirecting')
    ->expect(Redirect::class)
    ->not()
    ->toBeUsedIn('App\Http\Controllers');

arch('Migrations follow naming convention', function () {
    $migrations = glob(database_path('migrations/*.php'));

    foreach ($migrations as $migration) {
        $filename = pathinfo($migration, PATHINFO_FILENAME);
        expect($filename)->toMatch('/^\d{4}_\d{2}_\d{2}_\d{6}_.+$/');
    }
});
