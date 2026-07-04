<?php

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;

it('sets status from session when baseId matches', function () {
    $baseId = 'media-manager-123';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'base_id' => $baseId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    expect($component->status)->toBeArray()
        ->and($component->status['message'])->toBe('Test status message');
});

it('does not set status if baseId does not match', function () {
    $baseId = 'media-manager-123';
    $differentBaseId = 'other-initiator';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'base_id' => $differentBaseId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    expect($component->status)->toBeNull();
});

it('renders the status partial view', function () {
    $baseId = 'media-manager-123';
    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(View::class);
});

it('renders the status message in the view when baseId matches (plain)', function () {
    $baseId = 'media-manager-123';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'base_id' => $baseId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag, // pass attributes
    ])->render();

    expect($html)->toContain('Test status message');
    expect($html)->toContain('mle-status-message-success');
});

it('renders the status message in the view when baseId matches (bootstrap-5)', function () {
    $baseId = 'media-manager-123';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'base_id' => $baseId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'bootstrap-5',
        ],
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag, // pass attributes
    ])->render();

    expect($html)->toContain('Test status message');
    expect($html)->toContain('alert-success');
});

it('does not render the status message when baseId does not match', function () {
    $baseId = 'media-manager-123';
    $differentBaseId = 'other-media-manager';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'base_id' => $differentBaseId,
        'type' => 'error',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag, // pass attributes
    ])->render();

    expect($html)->not()->toContain('Test status message');
});

it('sets status from validation error bag when present', function () {
    app()->setLocale('en');

    $baseId = 'media-manager-123';

    // Prepare an error bag with one message
    $errors = new ViewErrorBag;
    $errors->put('media_manager_'.$baseId, new MessageBag([
        'collection' => ['Collection is verplicht.'],
    ]));

    session()->put('errors', $errors);

    $component = new Status(
        id: $baseId,
        options: [
            'theme' => 'plain',
        ],
    );

    expect($component->status)->toBeArray()
        ->and($component->status['type'])->toBe('error')
        ->and($component->status['message'])->toContain('Collection is verplicht.');

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,
        'attributes' => new ComponentAttributeBag,
    ])->render();

    expect($html)->toContain('Collection is verplicht.');
})->todo();
