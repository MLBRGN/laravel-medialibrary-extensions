<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediaResponse
{
    public static function success(Request $request, string $initiatorId, string $mediaManagerId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, $mediaManagerId, 'success', $message, $extraData);
    }

    public static function error(Request $request, string $initiatorId, string $mediaManagerId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, $mediaManagerId, 'error', $message, $extraData, 422);
    }

    protected static function respond(Request $request, string $initiatorId, string $mediaManagerId, string $type, string $message, array $extraData = [], int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            // camelCase for JSON (JS-friendly)
            $base = compact('initiatorId', 'type', 'message');

            return response()->json(array_merge($base, $extraData), $status);
        }

        // snake_case for response (PHP convention)
        $base = [
            'initiator_id' => $initiatorId,
            'media_manager_id' => $mediaManagerId,
            'type' => $type,
            'message' => $message,
        ];
//        Log::info('MediaResponse - base: '.json_encode($base));

        // Take the previous URL and append "#initiatorId"
        $targetUrl = url()->previous().'#'.$mediaManagerId;

        $redirect = redirect()
            ->to($targetUrl)
            ->with(status_session_prefix(), $base);

        // Add errors to redirect if provided
        if (! empty($extraData['errors'])) {
            $redirect->withErrors($extraData['errors']);
        }

        return $redirect;
    }
}
