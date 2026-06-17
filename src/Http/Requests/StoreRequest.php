<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

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
        $model = $this->resolveModel();

        return [
            'model_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $resolvedClass = $value;
                    if (class_exists(Relation::class)) {
                        $resolvedClass = Relation::getMorphedModel($value) ?? $value;
                    }

                    // check if the object has HasMediaExtended as one of its parents or implements it
                    if (
                        ! is_subclass_of($resolvedClass, HasMediaExtended::class)
                    ) {
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

        if (! $this->isTemporaryUpload()) {

            $model = $this->resolveModel();

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

        return new MaxTemporaryUploadCount(
            $collections,
            $maxItems,
            $this->input('instance_id'),
            $dataSource
        );
    }
}
