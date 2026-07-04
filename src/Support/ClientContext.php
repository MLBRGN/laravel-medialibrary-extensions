<?php

namespace Mlbrgn\MediaLibraryExtensions\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientContext
{
    public function __construct(
        protected Request $request
    ) {}

    public function get(): string
    {
        return $this->resolve();
    }

    public function resolve(): string
    {

        // Client token is a unique identifier for a client (browser)
        // Not that security-sensitive, used for identifying temporary uploads in conjunction with instanceId

        // 1. request input
        // Browser tests can consistently inject the token into every request, avoiding dependence on sessions or queued cookies,
        if ($token = $this->request->input('client_token')) {
            return $token;
        }

        // 2. request attribute (best / middleware-provided)
        if ($token = $this->request->attributes->get('mle_client_token')) {
            return $token;
        }

        // 3. session fallback (optional but useful for tests)
        if ($this->request->hasSession()) {
            if ($token = $this->request->session()->get('mle_client_token')) {
                return $token;
            }
        }

        // 4. cookie (cross-request persistence)
        if ($token = $this->request->cookie('mle_client_token')) {
            return $token;
        }

        // 5. generate ONLY if absolutely missing
        $token = (string) Str::ulid();

        // set to the request for the rest of request lifecycle
        $this->request->attributes->set('mle_client_token', $token);

        // store in session
        if ($this->request->hasSession()) {
            $this->request->session()->put('mle_client_token', $token);
        }

        // store cookie
        Cookie::queue(
            Cookie::forever('mle_client_token', $token)
        );

        return $token;
    }

}
