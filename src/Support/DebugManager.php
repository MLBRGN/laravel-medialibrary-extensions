<?php

namespace Mlbrgn\MediaLibraryExtensions\Support;

use Illuminate\Support\Facades\Log;

class DebugManager
{
    protected static array $components = [];

    protected static array $scopeStack = [];

    public static function pushScope(string $id): void
    {
//        Log::info('DebugManager: Pushing scope: ' . $id);
        static::$scopeStack[] = $id;
    }

    public static function popScope(?string $id = null): void
    {
//        Log::info('DebugManager: popping scope: ' . $id);

        if ($id === null) {
            array_pop(static::$scopeStack);

            return;
        }

        $last = end(static::$scopeStack);
        if ($last === $id) {
            array_pop(static::$scopeStack);
        }
    }

    public static function getCurrentScope(): ?string
    {
        return empty(static::$scopeStack) ? null : end(static::$scopeStack);
    }

    public static function register(string $id, string $name, array $config, array $options): void
    {
        if (! config('medialibrary-extensions.debug')) {
            return;
        }

        $scope = static::getCurrentScope();

        static::$components[$scope ?? 'global'][$id] = [
            'name' => $name,
            'config' => $config,
            'options' => $options,
        ];
    }

    public static function getRegisteredComponents(?string $scope = null): array
    {
        $components = [];

        if ($scope) {
            $components = static::$components[$scope] ?? [];

            // If the scope itself is registered in 'global', include it
            if (isset(static::$components['global'][$scope])) {
                $components = [$scope => static::$components['global'][$scope]] + $components;
            }

            // Also check if the scope was changed (e.g. MediaManager appends -mmm)
            // We search through 'global' for any component that might match the base ID
            foreach (static::$components['global'] ?? [] as $id => $data) {
                if ($id !== $scope && (str_starts_with($id, $scope) || str_starts_with($scope, $id))) {
                    if (! isset($components[$id])) {
                        $components = [$id => $data] + $components;
                    }
                }
            }

            return $components;
        }

        return static::$components[static::getCurrentScope() ?? 'global'] ?? [];
    }

    public static function reset(): void
    {
        static::$components = [];
        static::$scopeStack = [];
    }
}
