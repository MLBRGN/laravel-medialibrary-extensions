<div class="mlbrgn-mle-component theme-bootstrap-5 mle-media-lab">
    <div class="media-lab-preview-grid" data-media-manager-preview-grid>
        <div class="mle-media-lab-original">
            <div class="media-lab-title">
                {{ __('media-library-extensions::messages.original') }}
            </div>
            @if(method_exists($medium->model, 'getArchivedOriginalUrlFor'))
                <div class="mlbrgn-mle-component theme-bootstrap-5 media-manager-preview-container" data-media-manager-preview-container="">
                    <div class="media-manager-preview-item-container" data-bs-toggle="modal" data-bs-target="#alien-multiple-mmm-mod">
                        <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}" alt="" class="media-manager-image-preview">
                    </div>
                    <div class="media-manager-preview-menu">
                        <div class="media-manager-preview-image-menu-start">
                            test
                        </div>
                        <div class="media-manager-preview-image-menu-end">
                            <form action="{{ route('admin.media.restore-original', $medium) }}" method="POST">
                                @csrf
                                <button
    {{--                                type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"--}}
                                    type="submit"
                                    class="mle-button mle-button-submit mle-button-icon btn btn-primary"
                                    title="{{ __('media-library-extensions::messages.restore_original') }}"
    {{--                                data-action="restore-original-medium"--}}
    {{--                                data-route="{{ $getConfig('mediumDestroyRoute') }}"--}}
    {{--                                @disabled($disabled)--}}
                                >
                                    <x-mle-shared-icon
                                        name="{{ config('media-library-extensions.icons.restore') }}"
                                        :title="__('media-library-extensions::messages.restore_original')"
                                    />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
{{--                TODO use media-empty-state--}}
                Geen origineel opgeslagen
            @endif
        </div>
        <div class="mle-media-lab-base">
            <div class="media-lab-title">
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
//                                    'showUploadForm' => false,
                                    'showUploadForms' => false,
                                ]"
                :single-medium="$medium"
            />
        </div>
        <div class="mle-media-lab-conversions">
            <div class="media-lab-title">
                {{ __('media-library-extensions::messages.conversion') }}
            </div>
{{--            TODO this is not working the way it should--}}
            <x-mle-media-preview
                :id="'conversion-'.$medium->id"
                :medium="$medium"
                :model-or-class-name="$medium->model"
                :options="[
                    'showMenu' => true,
                    'showDestroyButton' => false,
                    'showSetAsFirstButton' => false,
                    'showMediaEditButton' => false,
                ]"
            >
                @php
                    $firstConversion = array_key_first($medium->extra_meta['conversions'] ?? []);
                @endphp

                @if($firstConversion)
                    <x-mle-image-responsive
                        :medium="$medium"
                        :conversions="[$firstConversion]"
                        class="mx-auto dummy-mm-item"
                    />
                @endif
{{--                @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)--}}
{{--                    <x-mle-image-responsive--}}
{{--                        :medium="$medium"--}}
{{--                        :conversions="[$conversionName]"--}}
{{--                        class="mx-auto dummy-mm-item"--}}
{{--                    />--}}
{{--                @endforeach--}}
            </x-mle-media-preview>
    
        </div>
    </div>
</div>

{{--        <div class="mlbrgn-mle-component theme-bootstrap-5 media-manager-preview-container" data-media-manager-preview-container="">--}}
{{--            <div class="media-manager-preview-item-container" data-bs-toggle="modal" data-bs-target="#alien-multiple-mmm-mod">--}}
{{--                @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)--}}
{{--                <x-mle-image-responsive--}}
{{--                    :medium="$medium"--}}
{{--                    :conversions="[$conversionName]"--}}
{{--                    class="mx-auto dummy-mm-item"--}}
{{--                />--}}
{{--                @endforeach--}}
{{--                <img src="{{ $medium->model->getArchivedOriginalUrlFor($medium) }}" alt="" class="media-manager-image-preview">--}}
{{--            </div>--}}
{{--            <div class="media-manager-preview-menu">--}}
{{--                <div class="media-manager-preview-image-menu-start">--}}
{{--                    test--}}
{{--                </div>--}}
{{--                <div class="media-manager-preview-image-menu-end">--}}
{{--                    <form action="{{ route('admin.media.restore-original', $medium) }}" method="POST">--}}
{{--                        @csrf--}}
{{--                        <button--}}
{{--                            --}}{{--                                type="{{ $getConfig('useXhr') ? 'button' : 'submit' }}"--}}
{{--                            type="submit"--}}
{{--                            class="mle-button mle-button-submit mle-button-icon btn btn-primary"--}}
{{--                            title="{{ __('media-library-extensions::messages.restore_original') }}"--}}
{{--                            --}}{{--                                data-action="restore-original-medium"--}}
{{--                            --}}{{--                                data-route="{{ $getConfig('mediumDestroyRoute') }}"--}}
{{--                            --}}{{--                                @disabled($disabled)--}}
{{--                        >--}}
{{--                            <x-mle-shared-icon--}}
{{--                                name="{{ config('media-library-extensions.icons.restore') }}"--}}
{{--                                :title="__('media-library-extensions::messages.restore_original')"--}}
{{--                            />--}}
{{--                        </button>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}