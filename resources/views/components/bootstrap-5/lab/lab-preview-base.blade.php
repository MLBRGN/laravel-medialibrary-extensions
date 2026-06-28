<div 
    class="mle-media-lab-preview-base" 
    data-mle-media-lab-preview-base
    id="{{ $getDomId() }}"
>
    <div class="mle-media-lab-title">
        {{ __('medialibrary-extensions::messages.base') }}
    </div>
    <div>
        Media id {{ $media->id }}
    </div>
    <x-mle-media-manager-single
        id="{{ $id }}"
        :model-or-class-name="$media->model"
        :collections="['image' => $media->collection_name]"
        :options="$getOptions()"
        :single-media="$media"
    />
    <div class="mle-media-lab-info">
        <div class="mle-info-panel">
            <div class="mle-info-row mle-info-header">
                <div>&nbsp;</div>
                <div>{{ __('medialibrary-extensions::messages.dimensions') }}</div>
                <div>{{ __('medialibrary-extensions::messages.ratio') }}</div>
            </div>

            <div class="mle-info-row">
                <div>{{ __('medialibrary-extensions::messages.actual') }}</div>
                <div>{{ $imageInfo['dimensions'] ?? '?' }}</div>
                <div>{{ $imageInfo['approx_label'] ?? ($imageInfo['ratio'] . ':1') }}</div>
            </div>

            <div class="mle-info-row">
                <div>{{ __('medialibrary-extensions::messages.required') }}</div>
                <div>
                    ≥ {{ $imageInfo['minWidth'] ?? '?' }} × {{ $imageInfo['minHeight'] ?? '?'  }}<br>
                    ≤ {{ $imageInfo['maxWidth'] ?? '?' }} × {{ $imageInfo['maxHeight'] ?? '?'  }}
                </div>
                <div>{{ $imageInfo['requiredLabel'] ?? '' }}</div>
            </div>

            <div class="mle-info-row">
                <div></div>
                <div>
                     <span
                         @class([
                             'mle-button-pseudo mle-button-icon-pseudo mle-button-icon-pseudo-small mle-button-no-hover',
                             'mle-button-icon-pseudo-valid' => !$imageInfo['tooWide'] && !$imageInfo['tooTall'] && !$imageInfo['tooNarrow'] && !$imageInfo['tooShort'],
                             'mle-button-icon-pseudo-invalid' => $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort'],
                         ])
                         title="{{ __('medialibrary-extensions::messages.edit') }}"
                     >
                        <x-mle-shared-icon
                            name="{{ $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort'] ? config('medialibrary-extensions.icons.x') : config('medialibrary-extensions.icons.check') }}"
                            title="{{ $imageInfo['tooWide'] || $imageInfo['tooTall'] || $imageInfo['tooNarrow'] || $imageInfo['tooShort']
                                ? __('medialibrary-extensions::messages.does_not_meet_requirements')
                                : __('medialibrary-extensions::messages.meets_requirements') }}"
                        />
                    </span>
                </div>
                <div>
                     <span
                         @class([
                             'mle-button-pseudo mle-button-icon-pseudo mle-button-icon-pseudo-small mle-button-no-hover',
                             'mle-button-icon-pseudo-valid' => $imageInfo['ratioOk'] || !$imageInfo['requiredValue'],
                             'mle-button-icon-pseudo-invalid' => !$imageInfo['ratioOk'] && $imageInfo['requiredValue'],
                         ])
                         title="{{ __('medialibrary-extensions::messages.edit') }}"
                     >
                        <x-mle-shared-icon
                            name="{{ !$imageInfo['ratioOk'] && $imageInfo['requiredValue'] ? config('medialibrary-extensions.icons.x') : config('medialibrary-extensions.icons.check') }}"
                            title="{{ !$imageInfo['ratioOk'] && $imageInfo['requiredValue']
                                ? __('medialibrary-extensions::messages.does_not_meet_requirements')
                                : __('medialibrary-extensions::messages.meets_requirements') }}"
                        />
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>