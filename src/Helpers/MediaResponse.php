<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaResponse
{
    public static function success(Request $request, string $initiatorId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'success', $message, $extraData);
    }

    public static function error(Request $request, string $initiatorId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'error', $message, $extraData);
    }

//    protected static function respond(Request $request, string $initiatorId, string $type, string $message, array $extraData = []): JsonResponse|RedirectResponse
//    {
//        $base = compact('initiatorId', 'type', 'message');
//
//        if ($request->expectsJson()) {
//            return response()->json(array_merge($base, $extraData));
//        }
//
//        return redirect()->back()->with(status_session_prefix(), $base);
//    }

    protected static function respond(Request $request, string $initiatorId, string $type, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            // camelCase for JSON (JS-friendly)
            $base = compact('initiatorId', 'type', 'message');
            return response()->json(array_merge($base, $extraData));
        }

        // snake_case for session (PHP convention)
        $base = [
            'initiator_id' => $initiatorId,
            'type' => $type,
            'message' => $message,
        ];
        return redirect()->back()->with(status_session_prefix(), $base);
    }
}
