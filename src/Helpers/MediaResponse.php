<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediaResponse
{
    public static function success(Request $request, string $baseId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $baseId, 'success', $message, $extraData);
    }

    public static function error(Request $request, string $baseId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $baseId, 'error', $message, $extraData, 422);
    }

    protected static function respond(Request $request, string $baseId, string $type, string $message, array $extraData = [], int $status = 200): JsonResponse|RedirectResponse
    {
        Log::debug('MediaResponse.respond expectsJson: '.($request->expectsJson() ? 'true' : 'false'));
        if ($request->expectsJson()) {
            // camelCase for JSON (JS-friendly)
            $base = [
                'baseId' => $baseId,
                'type' => $type,
                'message' => $message,
            ];
            $response = response()->json(array_merge($base, $extraData), $status);

            // If a client token is provided, set it as a cookie so subsequent XHRs can read it
            if (! empty($extraData['client_token']) && is_string($extraData['client_token'])) {
                // default: 30 days
                $minutes = 60 * 24 * 30;
                $response->cookie('mle_client_token', $extraData['client_token'], $minutes, '/');
            }

            Log::debug('MediaResponse.json');
            return $response;
        }

        Log::debug('MediaResponse.redirect');
        // snake_case for response (PHP convention)
        $base = [
            'base_id' => $baseId,
            'type' => $type,
            'message' => $message,
        ];

        // Take the previous URL and append "#baseId"
        $targetUrl = url()->previous().'#'.$baseId;// had to add a hidden <a> scroll element to the media manager view, for baseId !== domId

        Log::debug('targetUrlL '.$targetUrl);
        $redirect = redirect()
            ->to($targetUrl)
            ->with(status_session_prefix(), $base);

        // Add errors to redirect if provided
        if (! empty($extraData['errors'])) {
            $redirect->withErrors($extraData['errors']);
        }

        // If a client token is provided, also attach it as a cookie on redirect responses
        if (! empty($extraData['client_token']) && is_string($extraData['client_token'])) {
            // default: 30 days
            $minutes = 60 * 24 * 30;
            $redirect->withCookie(cookie('mle_client_token', $extraData['client_token'], $minutes, '/'));
        }

        return $redirect;
    }
}
