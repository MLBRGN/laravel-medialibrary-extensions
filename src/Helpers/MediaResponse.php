<?php

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MediaResponse
{
    public static function success(Request $request, string $initiatorId, string $message): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'success', $message);
    }

    public static function error(Request $request, string $initiatorId, string $message): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'error', $message);
    }

    protected static function respond(Request $request, string $initiatorId, string $type, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(compact('initiatorId', 'type', 'message'));
        }

        return redirect()->back()->with(status_session_prefix(), compact('initiatorId', 'type', 'message'));
    }
}
