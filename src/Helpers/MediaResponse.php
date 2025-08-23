<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

namespace Mlbrgn\MediaLibraryExtensions\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class MediaResponse
{
    public static function success(Request $request, string $initiatorId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'success', $message, $extraData);
    }

    public static function error(Request $request, string $initiatorId, string $message, array $extraData = []): JsonResponse|RedirectResponse
    {
        return self::respond($request, $initiatorId, 'error', $message, $extraData, 422);
    }
//    public static function error(Request $request, string $initiatorId, string $message, array $extraData = []): JsonResponse|RedirectResponse
//    {
//        return self::respond($request, $initiatorId, 'error', $message, $extraData);
//    }

    protected static function respond(Request $request, string $initiatorId, string $type, string $message, array $extraData = [], int $status = 200): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            // camelCase for JSON (JS-friendly)
            $base = compact('initiatorId', 'type', 'message');
            return response()->json(array_merge($base, $extraData), $status);
        }

        // snake_case for session (PHP convention)
        $base = [
            'initiator_id' => $initiatorId,
            'type' => $type,
            'message' => $message,
        ];

        // Add errors to Laravel's default error bag if provided
        if (!empty($extraData['errors'])) {
            $errors = $extraData['errors'];

            // Convert array of errors into a MessageBag
            $messageBag = new MessageBag($errors);

            // Put it into Laravel's default error bag
            $errorBag = session()->get('errors', new ViewErrorBag());
            $errorBag->put('default', $messageBag);
            session()->flash('errors', $errorBag);
        }

        // Take the previous URL and append "#initiatorId"
        $targetUrl = url()->previous() . '#' . $initiatorId;

        return redirect()
            ->to($targetUrl)
            ->with(status_session_prefix(), $base);
    }
}
