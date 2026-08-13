<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;

abstract class StoreRequest extends MediaManagerRequest
{
    public function authorize(): bool
    {
        return $this->authorizeMediaUpload();
    }

    public function rules(): array
    {

        return [];
    }

    protected function modelRules(): array
    {
        $model = $this->resolveRequestModel();

        return [
            'model_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (! $this->resolveModelClassFromInput()) {
                        $fail('The selected model type is invalid.');
                    }
                },
            ],

            'model_id' => [
                Rule::requiredIf(! $this->isTemporaryUpload()),
                function ($attribute, $value, $fail) use ($model) {
                    if (
                        ! $this->isTemporaryUpload()
                        && ! $model
                    ) {
                        $fail('The selected model id is invalid.');
                    }
                },
            ],
        ];
    }

    protected function uploadLimitRule(
        array $collections,
        int $maxItems
    ): ?ValidationRule {

        $dataSource = $this->input('data_source');
        $clientToken = $this->input('client_token');

        if (! $this->isTemporaryUpload()) {

            $model = $this->resolveRequestModel();

            if (! $model) {
                return null;
            }

            return new MaxMediaCount(
                $model,
                $collections,
                $maxItems,
                $dataSource
            );
        }

        $baseId = (string) $this->input('base_id');
        $derivedInstanceId = InstanceManager::getInstanceId($baseId);

        return new MaxTemporaryUploadCount(
            $collections,
            $maxItems,
            $derivedInstanceId,
            $dataSource,
            $clientToken
        );
    }
}
