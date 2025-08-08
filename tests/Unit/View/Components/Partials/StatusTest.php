<?php

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
})->todo();

it('renders the status partial view', function () {
    $component = new Status(
        id: 'status-1',
        frontendTheme: 'plain',
        initiatorId: 'initiator-123',
    );

    $view = $component->render();

    expect($view)->toBeInstanceOf(\Illuminate\View\View::class);
});
