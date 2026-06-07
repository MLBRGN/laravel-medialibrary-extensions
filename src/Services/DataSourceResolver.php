<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

class DataSourceResolver
{
    public function resolveConnection(?string $dataSource): string
    {
        if ($dataSource === null || $dataSource === '') {
            return config('database.default');
        }

        $connection = config("medialibrary-extensions.data_sources.$dataSource.connection");

        abort_unless(
            $connection !== null,
            500,
            "Data source [$dataSource] has no connection configured."
        );

        return $connection === 'default'
            ? config('database.default')
            : $connection;
    }

    public function resolveRepository(?string $repository = null): string
    {
        return $repository ?? 'default';
    }
}
