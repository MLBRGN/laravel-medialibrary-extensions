<x-mle-lab-preview
    class="mle-media-lab-preview-original"
    title="{{ __('media-library-extensions::messages.original') }}"
    :model-or-class-name="$medium->model"
    data-media-lab-preview-original
>
    @if(method_exists($medium->model, 'getArchivedOriginalUrlFor'))
        <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}"
             alt=""
             class="media-preview-image"
        >
    @else
        Geen origineel opgeslagen
    @endif

    <x-slot name="menuStart">

    </x-slot>

    <x-slot name="menuEnd">
        <x-mle-partial-medium-restore-form
            :model-or-class-name="$medium->model"
            :medium="$medium"
        />
    </x-slot>
    <x-slot name="imageInfo">
        <table class="mle-media-lab-info-table">
            <thead>
                <th>
    
                </th>
                <th>
                    {{ __('media-library-extensions::messages.dimensions') }}
                </th>
                <th>
                    {{ __('media-library-extensions::messages.ratio') }}
                </th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ __('media-library-extensions::messages.actual') }}
                    </td>
                    <td>
                        {{ $imageInfo['width'] }} × {{ $imageInfo['height'] }}
                    </td>
                    <td>
                        {{ $imageInfo['approx_label'] ?? ($imageInfo['ratio'] . ':1') }}
                    </td>
                </tr>
                <tr>
                    <td>
                      
                    </td>
                    <td>
                       
                    </td>
                    <td>
                   
                    </td>
                </tr>
                <tr>
                    <td>
    
                    </td>
                    <td>
                                <span>
                                </span>
                    </td>
                    <td>
                                <span>
                  
                                </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </x-slot>
</x-mle-lab-preview>