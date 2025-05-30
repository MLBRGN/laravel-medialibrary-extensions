{{--<pre>--}}
{{--        media-modal--}}
{{--        id - {{ $id }}--}}
{{--    </pre>--}}
@php
    $showHeader = false;
    $showBody = false;
    @endphp
<div class="mlbrgn-mle-component">
    <div
        {{ $attributes->merge(['class' => "media-modal modal fade $sizeClass"]) }}
        id="{{ $id }}"
        tabindex="-1"
        aria-labelledby="{{ $id }}-title"
        aria-hidden="true">

        <div class="modal-dialog modal-fullscreen-lg-down modal-almost-fullscreen">
            <div class="modal-content justify-content-center">
                @if ($showHeader === true)
                    <div class="modal-header">
                        <h1 class="h2 modal-title" id="{{ $id }}-title">{{ $title }}</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Sluit"></button>
                    </div>
                @else
                    <h1 class="modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
                @endif
                @if ($showBody === true)
                    <div class="modal-body {{ $noPadding ? 'p-0' : ''}}">
                @endif
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Sluit"></button>

                        {{-- important set click to open in modal to false otherwise endless inclusion --}}
                        <x-mle-media-carousel
                            id="{{ $id }}"
                            :model="$model"
                            :click-to-open-in-modal="false"
                            :media-collection="$mediaCollection"
                            :media-collections="$mediaCollections"/>
                @if($showBody)
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@once
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
@endonce

