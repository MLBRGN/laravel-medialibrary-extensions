<?php


use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Mlbrgn\MediaLibraryExtensions\Support\ClientContext;

beforeEach(function () {
    Cookie::spy();
});

function makeRequest(
    array  $input = [],
    array  $cookies = [],
    ?array $attributes = null,
    ?array $sessionData = null,
): Request
{
    $request = Request::create('/', 'GET', $input, $cookies);

    if ($attributes) {
        foreach ($attributes as $key => $value) {
            $request->attributes->set($key, $value);
        }
    }

    if ($sessionData !== null) {
        $session = new Store(
            'testing',
            new ArraySessionHandler(120)
        );

        foreach ($sessionData as $key => $value) {
            $session->put($key, $value);
        }

        $request->setLaravelSession($session);
    }

    return $request;
}

it('get delegates to resolve', function () {
    $request = makeRequest(
        input: ['client_token' => 'input-token']
    );

    $context = new ClientContext($request);

    expect($context->get())->toBe('input-token');
});

it('returns token from request input', function () {
    $request = makeRequest(
        input: ['client_token' => 'input-token']
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('input-token');
});

it('returns token from request attributes', function () {
    $request = makeRequest(
        attributes: [
            'mle_client_token' => 'attribute-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('attribute-token');
});

it('returns token from session', function () {
    $request = makeRequest(
        sessionData: [
            'mle_client_token' => 'session-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('session-token');
});

it('returns token from cookie', function () {
    $request = makeRequest(
        cookies: [
            'mle_client_token' => 'cookie-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('cookie-token');
});

it('prefers request input over all other sources', function () {
    $request = makeRequest(
        input: ['client_token' => 'input-token'],
        cookies: ['mle_client_token' => 'cookie-token'],
        attributes: ['mle_client_token' => 'attribute-token'],
        sessionData: ['mle_client_token' => 'session-token'],
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('input-token');
});

it('prefers request attributes over session and cookie', function () {
    $request = makeRequest(
        cookies: ['mle_client_token' => 'cookie-token'],
        attributes: ['mle_client_token' => 'attribute-token'],
        sessionData: ['mle_client_token' => 'session-token'],
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('attribute-token');
});

it('prefers session over cookie', function () {
    $request = makeRequest(
        cookies: ['mle_client_token' => 'cookie-token'],
        sessionData: ['mle_client_token' => 'session-token'],
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('session-token');
});

it('generates a token when none exists', function () {
    $request = makeRequest();

    $context = new ClientContext($request);

    $token = $context->resolve();

    expect($token)->not->toBeEmpty();

    expect(Str::isUlid($token))->toBeTrue();
});

it('stores generated token in request attributes', function () {
    $request = makeRequest();

    $context = new ClientContext($request);

    $token = $context->resolve();

    expect(
        $request->attributes->get('mle_client_token')
    )->toBe($token);
});

it('stores generated token in session', function () {
    $request = makeRequest(
        sessionData: []
    );

    $context = new ClientContext($request);

    $token = $context->resolve();

    expect(
        $request->session()->get('mle_client_token')
    )->toBe($token);
});

it('queues generated token cookie', function () {
    $request = makeRequest();

    $context = new ClientContext($request);

    $token = $context->resolve();

    Cookie::shouldHaveReceived('forever')
        ->once()
        ->with('mle_client_token', $token);

    Cookie::shouldHaveReceived('queue')
        ->once();
});

it('does not regenerate when session token exists', function () {
    $request = makeRequest(
        sessionData: [
            'mle_client_token' => 'existing-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('existing-token');

    Cookie::shouldNotHaveReceived('queue');
});

it('ignores empty input token', function () {
    $request = makeRequest(
        input: ['client_token' => ''],
        sessionData: [
            'mle_client_token' => 'session-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('session-token');
});

it('ignores empty attribute token', function () {
    $request = makeRequest(
        attributes: [
            'mle_client_token' => '',
        ],
        sessionData: [
            'mle_client_token' => 'session-token',
        ]
    );

    $context = new ClientContext($request);

    expect($context->resolve())
        ->toBe('session-token');
});

it('reuses generated token within the same request', function () {
    $request = makeRequest();

    $context = new ClientContext($request);

    $first = $context->resolve();
    $second = $context->resolve();

    expect($first)->toBe($second);
});

it('generates token when request has no session', function () {
    $request = makeRequest();

    $context = new ClientContext($request);

    expect(fn () => $context->resolve())
        ->not->toThrow(Exception::class);
});

it('does not overwrite existing session token', function () {
    $request = makeRequest(
        sessionData: [
            'mle_client_token' => 'existing-token',
        ]
    );

    $context = new ClientContext($request);

    $context->resolve();

    expect(
        $request->session()->get('mle_client_token')
    )->toBe('existing-token');
});

it('does not queue cookie when request input token exists', function () {
    $request = makeRequest(
        input: [
            'client_token' => 'input-token',
        ]
    );

    $context = new ClientContext($request);

    $context->resolve();

    Cookie::shouldNotHaveReceived('queue');
});
