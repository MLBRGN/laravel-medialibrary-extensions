<?php

use Illuminate\View\ComponentAttributeBag;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;

it('sets status from session when initiatorId matches', function () {
    $initiatorId = 'initiator-123';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
    );

    expect($component->status)->toBeArray()
        ->and($component->status['message'])->toBe('Test status message');
});

it('does not set status if initiatorId does not match', function () {
    $initiatorId = 'initiator-123';
    $differentInitiatorId = 'other-initiator';
    $statusKey = status_session_prefix();

    session()->put($statusKey, [
        'initiator_id' => $differentInitiatorId,
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
    );

    expect($component->status)->toBeNull();
});

it('renders the status partial view', function () {
    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: 'initiator-123',
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});

it('renders the status message in the view when initiatorId matches (plain)', function () {
    $initiatorId = 'initiator-123';
    $statusKey = status_session_prefix($initiatorId);

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
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
    $initiatorId = 'initiator-123';
    $statusKey = status_session_prefix($initiatorId);

    session()->put($statusKey, [
        'initiator_id' => $initiatorId,
        'type' => 'success',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'bootstrap-5',
        initiatorId: $initiatorId,
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
    $differentInitiatorId = 'other-initiator';
    $statusKey = status_session_prefix($differentInitiatorId);

    session()->put($statusKey, [
        'initiator_id' => $differentInitiatorId,
        'type' => 'error',
        'message' => 'Test status message',
    ]);

    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: $initiatorId,
    );

    // Render the component with attributes injected
    $html = $component->render()->with([
        'status' => $component->status,               // pass the status property
        'attributes' => new ComponentAttributeBag(), // pass attributes
    ])->render();

    expect($html)->not()->toContain('Test status message');
});
