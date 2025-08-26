<?php

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

it('runs without touching the real system', function () {
    // Prevent filesystem writes
    File::shouldReceive('delete')->zeroOrMoreTimes();
    File::shouldReceive('deleteDirectory')->zeroOrMoreTimes();

    // Fake composer.json contents
    file_put_contents(base_path('composer.json'), json_encode([
        'require' => [
            'mlbrgn/laravel-medialibrary-extensions' => '^1.0',
        ],
    ]));

    // Mock Process so composer update is not executed
    $mockProcess = Mockery::mock(Process::class);
    $mockProcess->shouldReceive('setTty')->andReturnSelf();
    $mockProcess->shouldReceive('run')->andReturn(0);

    // Swap Process factory
    Process::macro('fromShellCommandline', fn () => $mockProcess);

    $this->artisan('media-library-extensions:toggle-repository --force')
        ->assertExitCode(0)
        ->expectsOutputToContain('📦 Running composer update');
})->todo('too complicated for now');
