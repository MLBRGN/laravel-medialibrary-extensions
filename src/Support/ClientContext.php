<?php

namespace Mlbrgn\MediaLibraryExtensions\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
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

//    public function resolve(): string
//    {
//        // 1. request attribute (best / middleware-provided)
//        if ($token = $this->request->attributes->get('mle_client_token')) {
//            return $token;
//        }
//
//        // 2. cookie (cross-request persistence)
//        if ($token = $this->request->cookie('mle_client_token')) {
//            return $token;
//        }
//
//        // 3. session fallback (optional but useful for tests)
//        if ($this->request->hasSession()) {
//            if ($token = $this->request->session()->get('mle_client_token')) {
//                return $token;
//            }
//        }
//
//        // 4. generate ONLY if absolutely missing
//        $token = (string) Str::ulid();
//
//        // persist for rest of request lifecycle
//        $this->request->attributes->set('mle_client_token', $token);
//
//        if ($this->request->hasSession()) {
//            $this->request->session()->put('mle_client_token', $token);
//        }
//
//        return $token;
//    }

    public function resolve(): string
    {
        if ($token = $this->request->attributes->get('mle_client_token')) {
            return $token;
        }

        if ($token = $this->request->cookie('mle_client_token')) {
            return $token;
        }

        if ($this->request->hasSession()) {
            if ($token = $this->request->session()->get('mle_client_token')) {
                return $token;
            }
        }

        $token = (string) Str::ulid();

        $this->request->attributes->set('mle_client_token', $token);

        if ($this->request->hasSession()) {
            $this->request->session()->put('mle_client_token', $token);
        }

        // 🔥 THIS IS THE MISSING PIECE
        Cookie::queue(
            Cookie::forever('mle_client_token', $token)
        );

        return $token;
    }
}
