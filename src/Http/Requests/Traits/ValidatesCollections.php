<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests\Traits;


use Illuminate\Validation\Validator;

trait ValidatesCollections
{
    /**
     * Hook into Laravel's validator to enforce:
     *  - only allowed collection keys
     *  - at least one key present and non-empty
     */
    public function addCollectionsValidation(Validator $validator): void
    {
        $collections = $this->input('collections', []);

        $allowedKeys = ['image', 'document', 'audio', 'video', 'youtube'];

        // 1️⃣ Reject unexpected keys
        $invalidKeys = array_diff(array_keys($collections), $allowedKeys);
        if (!empty($invalidKeys)) {
            $validator->errors()->add(
                'collections',
                'Invalid collection keys: ' . implode(', ', $invalidKeys)
            );
        }

        // 2️⃣ Require at least one allowed, non-empty key
        $nonEmpty = collect($collections)
            ->only($allowedKeys)
            ->filter(fn($v) => filled($v));

        if ($nonEmpty->isEmpty()) {
            $validator->errors()->add(
                'collections',
                'At least one collection (image, document, audio, video, or youtube) must be set.'
            );
        }
    }
}
