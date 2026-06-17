<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class MlbrgnClientTokenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->hasCookie('mle_client_token')) {
            $token = (string) Str::ulid();
            $response->withCookie(cookie()->forever('mle_client_token', $token));
        }

        return $response;
    }
}
