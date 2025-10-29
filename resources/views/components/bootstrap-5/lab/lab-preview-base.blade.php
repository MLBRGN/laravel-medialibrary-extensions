<div class="mle-media-lab-preview-base" data-media-lab-preview-base>
    <div class="mle-media-lab-title">
        {{ __('media-library-extensions::messages.base') }}
    </div>
    <x-mle-media-manager-single
        class=""
        id="medium-{{$medium->id}}"
        :model-or-class-name="$medium->model"
        :collections="['image' => $medium->collection_name]"
        :options="[
            'showDestroyButton' => false,
            'showSetAsFirstButton' => false,
            'showMediaEditButton' => true,
            'showMenu' => true,
            'showUploadForms' => false,
        ]"
        :single-medium="$medium"
    />
    <div class="media-lab-info">

        @php
            use Illuminate\Support\Arr;
        
            $config = config('media-library-extensions');
            $maxW = $config['max_image_width'] ?? 1920;
            $maxH = $config['max_image_height'] ?? 1080;
        
            if (!empty($requiredAspectRatio)) {
                $requiredLabel = array_key_first($requiredAspectRatio);
                $requiredValue = $requiredAspectRatio[$requiredLabel];
            } else {
                $requiredLabel = __('media-library-extensions::messages.unknown');
                $requiredValue = null;
            }

            $tooWide = $imageInfo['width'] > $maxW;
            $tooTall = $imageInfo['height'] > $maxH;
        
            $ratioOk = false;
            if (!empty($imageInfo['ratio']) && !empty($requiredValue)) {
                $tolerance = 0.02; // ~2% tolerance
                $ratioOk = abs($imageInfo['ratio'] - $requiredValue) < $tolerance;
            }
            
        @endphp
        
        <div class="mle-media-lab-info">
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
                            {{ __('media-library-extensions::messages.required') }}
                        </td>
                        <td>
                            ≤ {{ $maxW }} × {{ $maxH }}
                        </td>
                        <td>
                            @if ($requiredLabel !==  __('media-library-extensions::messages.unknown'))
                                {{ $requiredLabel }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>

                        </td>
                        <td>
                            <span
                                @class([
                                    'mle-button-pseudo mle-button-icon-pseudo',
                                    'mle-button-icon-pseudo-valid' => !$tooWide && !$tooTall,
                                    'mle-button-icon-pseudo-invalid' => $tooWide || $tooTall,
                                ])
                                {{--                                title="{{ __('media-library-extensions::messages.edit') }}"--}}
                            >
                                <x-mle-shared-icon
                                    name="{{ $tooWide || $tooTall ? config('media-library-extensions.icons.x') : config('media-library-extensions.icons.check') }}"
                                    title="{{ $tooWide || $tooTall
                                    ? __('media-library-extensions::messages.does_not_meet_requirements')
                                    : __('media-library-extensions::messages.meets_requirements') }}"
                                />
                            </span>
                        </td>
                        <td>
                            <span
                                @class([
                                'mle-button-pseudo mle-button-icon-pseudo',
                                'mle-button-icon-pseudo-valid' => $ratioOk || !$requiredValue,
                                'mle-button-icon-pseudo-invalid' => !$ratioOk && $requiredValue,
                            ])
                                class="mle-button-pseudo mle-button-icon-pseudo"
{{--                                title="{{ __('media-library-extensions::messages.edit') }}"--}}
                            >
                                <x-mle-shared-icon
                                    name="{{ !$ratioOk && $requiredValue ? config('media-library-extensions.icons.x') : config('media-library-extensions.icons.check') }}"
                                    title="{{ !$ratioOk && $requiredValue
                                    ? __('media-library-extensions::messages.does_not_meet_requirements')
                                    : __('media-library-extensions::messages.meets_requirements') }}"
                                />
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>