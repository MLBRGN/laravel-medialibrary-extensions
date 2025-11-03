<div 
    class="mle-media-lab-preview-base" 
    data-mle-media-lab-preview-base
>
    <div class="mle-media-lab-title">
        {{ __('media-library-extensions::messages.base') }}
    </div>
    <x-mle-media-manager-single
        id="medium-{{$medium->id}}"
        :model-or-class-name="$medium->model"
        :collections="['image' => $medium->collection_name]"
        :options="$options"
        :single-medium="$medium"
    />
    <div class="mle-media-lab-info">
        <div class="mle-info-panel">
            <div class="mle-info-row mle-info-header">
                <div>&nbsp;</div>
                <div>{{ __('media-library-extensions::messages.dimensions') }}</div>
                <div>{{ __('media-library-extensions::messages.ratio') }}</div>
            </div>

            <div class="mle-info-row">
                <div>{{ __('media-library-extensions::messages.actual') }}</div>
                <div>{{ $imageInfo['dimensions'] ?? '?' }}</div>
                <div>{{ $imageInfo['approx_label'] ?? ($imageInfo['ratio'] . ':1') }}</div>
            </div>

            <div class="mle-info-row">
                <div>{{ __('media-library-extensions::messages.required') }}</div>
                <div>
                    ≤ {{ $imageInfo['maxWidth'] ?? '?' }} × {{ $imageInfo['maxHeight'] ?? '?'  }}<br>
                    ≥ {{ $imageInfo['minWidth'] ?? '?' }} × {{ $imageInfo['minHeight'] ?? '?'  }}</div>
                <div>{{ $imageInfo['requiredLabel'] ?? '' }}</div>
            </div>

            <div class="mle-info-row">
                <div></div>
                <div>
                     <span
                         @class([
                             'mle-button-pseudo mle-button-icon-pseudo mle-button-icon-pseudo-small',
                             'mle-button-icon-pseudo-valid' => !$imageInfo['tooWide'] && !$imageInfo['tooTall'] && !$imageInfo['tooNarrow'] && !$imageInfo['tooShort'],
                             'mle-button-icon-pseudo-invalid' => $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort'],
                         ])
                         title="{{ __('media-library-extensions::messages.edit') }}"
                     >
                        <x-mle-shared-icon
                            name="{{ $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort'] ? config('media-library-extensions.icons.x') : config('media-library-extensions.icons.check') }}"
                            title="{{ $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort']
                                ? __('media-library-extensions::messages.does_not_meet_requirements')
                                : __('media-library-extensions::messages.meets_requirements') }}"
                        />
                    </span>
                </div>
                <div>
                     <span
                         @class([
                             'mle-button-pseudo mle-button-icon-pseudo mle-button-icon-pseudo-small',
                             'mle-button-icon-pseudo-valid' => $imageInfo['ratioOk'] || !$imageInfo['requiredValue'],
                             'mle-button-icon-pseudo-invalid' => !$imageInfo['ratioOk'] && $imageInfo['requiredValue'],
                         ])
                         title="{{ __('media-library-extensions::messages.edit') }}"
                     >
                        <x-mle-shared-icon
                            name="{{ !$imageInfo['ratioOk'] && $imageInfo['requiredValue'] ? config('media-library-extensions.icons.x') : config('media-library-extensions.icons.check') }}"
                            title="{{ !$imageInfo['ratioOk'] && $imageInfo['requiredValue']
                                ? __('media-library-extensions::messages.does_not_meet_requirements')
                                : __('media-library-extensions::messages.meets_requirements') }}"
                        />
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>