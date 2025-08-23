<?php

use Illuminate\View\ComponentAttributeBag;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

it('sets status from session when initiatorId matches', function () {
    $initiatorId = 'media-manager-123';
    $mediaManagerId = 'media-manager-123';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    expect($component->status)->toBeArray()
        ->and($component->status['message'])->toBe('Test status message');
});

it('does not set status if initiatorId does not match', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $differentMediaManagerId = 'other-initiator';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $differentMediaManagerId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    expect($component->status)->toBeNull();
});

it('renders the status partial view', function () {
    $initiatorId = 'media-manager-123';
    $mediaManagerId = 'media-manager-123';$component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});

it('renders the status message in the view when initiatorId matches (plain)', function () {
    $initiatorId = 'media-manager-123';
    $mediaManagerId = 'media-manager-123';
    $statusKey = status_session_prefix($initiatorId);

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag(), // pass attributes
    ])->render();

    expect($html)->toContain('Test status message');
    expect($html)->toContain('mle-status-message-success');
});

it('renders the status message in the view when initiatorId matches (bootstrap-5)', function () {
    $initiatorId = 'media-manager-123';
    $mediaManagerId = 'media-manager-123';
    $statusKey = status_session_prefix($initiatorId);

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $mediaManagerId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'bootstrap-5',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag(), // pass attributes
    ])->render();

    expect($html)->toContain('Test status message');
    expect($html)->toContain('alert-success');
});

it('does not render the status message when initiatorId does not match', function () {
    $initiatorId = 'initiator-123';
    $mediaManagerId = 'media-manager-123';
    $differentInitiatorId = 'other-initiator';
    $differentMediaManagerId = 'other-media-manager';
    $statusKey = status_session_prefix($differentMediaManagerId);

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'media_manager_id' => $differentMediaManagerId,
        'type' => 'error',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag(), // pass attributes
    ])->render();

    expect($html)->not()->toContain('Test status message');
});

it('sets status from validation error bag when present', function () {
    app()->setLocale('en');

    $initiatorId = 'media-manager-456';
    $mediaManagerId = 'media-manager-123';

    // Prepare an error bag with one message
    $errors = new ViewErrorBag();
    $errors->put('media_manager_'.$initiatorId, new MessageBag([
        'collection' => ['Collection is verplicht.'],
    ]));

    session()->put('errors', $errors);

    $component = new Status(
        id: 'status-2',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
        mediaManagerId: $mediaManagerId,
    );

    expect($component->status)->toBeArray()
        ->and($component->status['type'])->toBe('error')
        ->and($component->status['message'])->toContain('Collection is verplicht.');

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,
        'attributes' => new ComponentAttributeBag(),
    ])->render();

    expect($html)->toContain('Collection is verplicht.');
})->skip();
