<?php

//
// Pest presets
//

arch()->preset()->php();
//arch()->preset()->security();
//arch()->preset()->laravel(); extracted test below and changed to our needs

//
// Copied tests from laravel pest preset (don't think it is possible to skip certain preset tests
//

arch('Traits must be traits')->expect('App\Traits')
    ->toBeTraits();

arch('Concerns must be traits')->expect('App\Concerns')
    ->toBeTraits();

arch('App should not contain Enums')->expect('App')
    ->not->toBeEnums()
    ->ignoring('App\Enums');

arch('App\Enums contains only Enums')->expect('App\Enums')
    ->toBeEnums()
    ->ignoring('App\Enums\Concerns');

arch('App\Features classes should be defined')->expect('App\Features')
    ->toBeClasses()
    ->ignoring('App\Features\Concerns');

arch('App\Features should contain resolve method')->expect('App\Features')
    ->toHaveMethod('resolve');

arch('App\Exceptions should implement Throwable')->expect('App\Exceptions')
    ->classes()
    ->toImplement('Throwable')
    ->ignoring('App\Exceptions\Handler');

arch('App classes do not implement Throwable')->expect('App')
    ->not->toImplement(Throwable::class)
    ->ignoring('App\Exceptions');

arch('App\Http\Middleware classes should have handle method')->expect('App\Http\Middleware')
    ->classes()
    ->toHaveMethod('handle');

arch('App\Models should extend Eloquent Model')->expect('App\Models')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\Scopes');

// Disabled test on Models with suffix 'Model'

// arch('App should not extend Model outside App\Models')->expect('App')
//     ->not->toExtend('Illuminate\Database\Eloquent\Model')
//     ->ignoring('App\Models');

arch('App\Http\Requests classes have suffix Request')->expect('App\Http\Requests')
    ->classes()
    ->toHaveSuffix('Request');

arch('App\Http\Requests extend FormRequest')->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch('App\Http\Requests contain rules method')->expect('App\Http\Requests')
    ->toHaveMethod('rules');

arch('App does not extend FormRequest outside App\Http\Requests')->expect('App')
    ->not->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->ignoring('App\Http\Requests');

// Disabled test Console Commands suffix
//        arch('Commands have Command suffix')->expect('App\Console\Commands')
//            ->classes()
//            ->toHaveSuffix('Command');

arch('App\Console\Commands extend Console Command')->expect('App\Console\Commands')
    ->classes()
    ->toExtend('Illuminate\Console\Command');

arch('App\Console\Commands contain handle method')->expect('App\Console\Commands')
    ->classes()
    ->toHaveMethod('handle');

arch('App does not extend Console Command outside App\Console\Commands')->expect('App')
    ->not->toExtend('Illuminate\Console\Command')
    ->ignoring('App\Console\Commands');

arch('App\Mail extends Mailable')->expect('App\Mail')
    ->classes()
    ->toExtend('Illuminate\Mail\Mailable');

// Disabled test on Mail implementing ShouldQueue
//        arch('Mail should implement ShouldQueue')-<expect('App\Mail')
//            ->classes()
//            ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

arch('App does not extend Mailable outside App\Mail')->expect('App')
    ->not->toExtend('Illuminate\Mail\Mailable')
    ->ignoring('App\Mail');

arch('App\Jobs implement ShouldQueue')->expect('App\Jobs')
    ->classes()
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');

arch('App\Jobs contain handle method')->expect('App\Jobs')
    ->classes()
    ->toHaveMethod('handle');

arch('App\Listeners contain handle method')->expect('App\Listeners')
    ->toHaveMethod('handle');

arch('App\Notifications extends Notification')->expect('App\Notifications')
    ->toExtend('Illuminate\Notifications\Notification');

arch('App does not extend Notification outside App\Notifications')->expect('App')
    ->not->toExtend('Illuminate\Notifications\Notification')
    ->ignoring('App\Notifications');

arch('App\Providers should have suffix ServiceProvider')->expect('App\Providers')
    ->toHaveSuffix('ServiceProvider');

arch('App\Providers should extend ServiceProvider')->expect('App\Providers')
    ->toExtend('Illuminate\Support\ServiceProvider');

// Disabled test on Providers usage in classes
//        Arch('Providers are not used in classes')->expect('App\Providers')
//            ->not->toBeUsed();

arch('App does not extend ServiceProvider outside App\Providers')->expect('App')
    ->not->toExtend('Illuminate\Support\ServiceProvider')
    ->ignoring('App\Providers');

arch('App classes do not have ServiceProvider suffix outside Providers')->expect('App')
    ->not->toHaveSuffix('ServiceProvider')
    ->ignoring('App\Providers');

arch('App classes do not have Controller suffix outside Controllers')->expect('App')
    ->not->toHaveSuffix('Controller')
    ->ignoring('App\Http\Controllers');

arch('App\Http\Controllers classes have suffix Controller')->expect('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

// Disabled test on Http classes being used only in Http namespace
//        arch('Http classes are only used in Http namespace')->expect('App\Http')
//            ->toOnlyBeUsedIn('App\Http');

// Disabled test on public methods restriction in Controllers
//
//        arch('Only common methods used in Controllers')->expect('App\Http\Controllers')
//            ->not->toHavePublicMethodsBesides(['__construct', '__invoke', 'index', 'show', 'create', 'store', 'edit', 'update', 'destroy', 'middleware']);
//

arch('App debug and environment methods are not used')->expect([
    'dd',
    'ddd',
    'dump',
    'env',
    //    'exit',
    'ray',
])->not->toBeUsed();

arch('App\Policies classes have suffix Policy')->expect('App\Policies')
    ->classes()
    ->toHaveSuffix('Policy');

//
// custom arch tests
//
arch('enums are enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('models are classes')
    ->expect('App\Models')
    ->toBeClasses();

arch('services namespace only includes classes')
    ->expect('App\Services')
    ->toBeClasses();

// although fortify publishes in actions, these classes don't follow actions conventions, therefor excluded
arch('actions to be invokable')
    ->expect('App\Actions')
    ->toHaveMethod('execute')
    ->ignoring('App\Actions\Fortify');

arch('No debugging statements left in code')
//    ->expect(['dd', 'dump', 'die', 'var_dump', 'ray', 'sleep', 'exit', 'print_r', 'echo', 'print', 'phpinfo'])
    ->expect(['dd', 'dump', 'die', 'var_dump', 'ray', 'sleep', 'print_r', 'echo', 'print', 'phpinfo', 'ray'])
    ->not->toBeUsed();

arch('Controllers have Controller suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

//arch('Mailable classes have Mailable suffix')
//    ->expect('App\Mail')
//    ->toHaveSuffix('Mailable');

arch('Controllers cannot be used anywhere')
    ->expect('App\Controller')
    ->toBeUsedInNothing();

arch('Requests have Request suffix')
    ->expect('App\Http\Requests')
    ->toHaveSuffix('Request');

arch('Actions have Actions suffix')
    ->expect('App\Actions')
    ->toHaveSuffix('Action')
    ->ignoring('App\Actions\Fortify');

arch('Providers have Provider suffix, are classes, extend ServiceProvider and implement nothing')
    ->expect('App\Providers')
    ->toHaveSuffix('Provider')
    ->toBeClasses()
    ->classes->toExtend('Illuminate\Support\ServiceProvider');
//    ->classes->toImplementNothing();

arch('Listeners have Listener suffix')
    ->expect('App\Listeners')
    ->toHaveSuffix('Listener');

arch('Listener classes should have handle method')
    ->expect('App\Listeners')
    ->toHaveMethod('handle');

arch('Seeders have Seeder suffix')
    ->expect('Database\Seeders')
    ->toHaveSuffix('Seeder');

arch('Factories have Factory suffix')
    ->expect('Database\Factories')
    ->toHaveSuffix('Factory');

//arch('Helpers have Helper suffix')
//    ->expect('App\Helpers')
//    ->toHaveSuffix('Helper');

arch('Do not use env helper in code')
    ->expect(['env'])
    ->not->toBeUsed();

arch('Mailables are extending the correct class')
    ->expect('App\Mail')
    ->toBeClasses()
//    ->classes->toBeFinal() // optional
    ->classes->toExtend('Illuminate\Mail\Mailable');
//    ->classes->toImplement('Illuminate\Contracts\Queue\ShouldQueue'); // optional

arch('Commands are extending the correct class')
    ->expect('App\Console\Commands')
    ->toBeClasses()
//    ->classes->toBeFinal() // optional
    ->classes->toExtend('Illuminate\Console\Command');

arch('Tests are using strict types and have the correct suffix')
    ->expect('Tests')
    ->and('Tests\Feature')->toHaveSuffix('Test')
    ->and('Tests\Unit')->toHaveSuffix('Test')
    ->and('Tests\Arch')->toHaveSuffix('Test');

arch('No direct database queries in controllers')
    ->expect(['DB::select', 'DB::insert', 'DB::update', 'DB::delete', 'DB::statement'])
//    ->in('app/Http/Controllers')
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
    ->expect(\Illuminate\Support\Facades\Redirect::class)
    ->not()
    ->toBeUsedIn('App\Http\Controllers');

arch('ensures migrations follow naming convention', function () {
    $migrations = glob(database_path('migrations/*.php'));

    foreach ($migrations as $migration) {
        $filename = pathinfo($migration, PATHINFO_FILENAME);
        expect($filename)->toMatch('/^\d{4}_\d{2}_\d{2}_\d{6}_.+$/');
    }
});
