<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests\demo;

use Illuminate\Foundation\Http\FormRequest;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;
use Mlbrgn\MediaLibraryExtensions\Models\demo\Alien;

class StoreIsolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dataSource = $this->input('data_source', 'demo_default');

        return [
            'manager_a_count' => [
                'required',
                new MinMediaCount(
                    Alien::class,
                    ['image' => 'alien-multiple-images'],
                    1,
                    null,
                    $dataSource
                )
            ],
            'manager_b_count' => [
                'required',
                new MaxMediaCount(
                    Alien::class,
                    ['image' => 'alien-multiple-images'],
                    2,
                    null,
                    $dataSource
                )
            ],
        ];
    }
}
