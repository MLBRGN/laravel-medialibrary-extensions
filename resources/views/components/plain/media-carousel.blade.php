<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>
    <div id="{{ $id }}"
         class="media-carousel plain-carousel mle-width-100 mle-height-100"
         data-carousel
         data-carousel-id="{{ $id }}">

        {{-- Indicators --}}
        <div
            class="media-carousel-indicators carousel-indicators {{ $mediaItems->count() < 2 ? 'mle-display-none' : '' }}">
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-slide-to="{{ $index }}"
                    class="{{ $loop->first ? 'active' : '' }}"
                    aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">
                </button>
            @endforeach
        </div>

        {{-- Slides --}}
        <div class="media-carousel-inner">
            @foreach($mediaItems as $index => $medium)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }} {{ $clickToOpenInModal ? 'mle-cursor-zoom-in' : '' }}">
                    <div class="carousel-item-wrapper d-flex align-items-center justify-content-center"
                         @if($clickToOpenInModal)
                             data-modal-trigger="#{{ $id }}-modal"
                        @endif>
                        @if($medium->hasCustomProperty('youtube-id'))
                            <x-mle-video-youtube
                                class="mle-video-responsive"
                                :medium="$medium"
                                :preview="$inModal ? false : true"
                                :youtube-id="$medium->getCustomProperty('youtube-id')"
                                :youtube-params="$inModal ? ['mute' => 1] : []"
                            />
                        @else
                            <x-mle-image-responsive
                                class="mle-image-responsive"
                                :medium="$medium"
                                :conversions="['16x9']"
                                sizes="100vw"
                                :alt="$medium->name"
                            />
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Prev/Next controls --}}
        @if ($mediaItems->count() > 1)
            <button type="button" class="carousel-control-prev" data-slide="prev">
                <span aria-hidden="true">&#10094;</span>
                <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
            </button>
            <button type="button" class="carousel-control-next" data-slide="next">
                <span aria-hidden="true">&#10095;</span>
                <span class="mle-visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
            </button>
        @endif
    </div>

    @if($clickToOpenInModal)
        <x-mle-media-modal
            :id="$id"
            :model="$model"
            :media-collection="$mediaCollection"
            :media-collections="$mediaCollections"
            title="Media carousel"/>
    @endif
</div>

@once
    <style>
        .mlbrgn-mle-component {
        .media-carousel {
            position: relative;
            overflow: hidden;
            border:10px solid orange;
        }

        .plain-carousel .carousel-inner {
            display: flex;
            transition: transform 0.5s ease-in-out;
            will-change: transform;
        }

        .plain-carousel .carousel-item {
            min-width: 100%;
            flex-shrink: 0;
            transition: opacity 0.5s ease-in-out;
            opacity: 0;
            position: absolute;
            top: 0;
            left: 0;
        }

        .plain-carousel .carousel-item.active {
            position: relative;
            opacity: 1;
            z-index: 1;
        }

        .plain-carousel .media-carousel-indicators button.active {
            background-color: #000;
        }

        .plain-carousel {
            position: relative;
            overflow: hidden;
        }

        .media-carousel-inner {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
            height: 100%;
        }

        .carousel-item {
            flex: 0 0 100%;
            max-width: 100%;
            display: none;
        }

        .carousel-item.active {
            display: block;
        }

        .carousel-control-prev,
        .carousel-control-next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 2rem;
            z-index: 10;
            cursor: pointer;
            color: black;
        }

        .carousel-control-prev {
            left: 1rem;
        }

        .carousel-control-next {
            right: 1rem;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #ccc;
            border: none;
            cursor: pointer;
        }

        .carousel-indicators button.active {
            background-color: #333;
        }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-carousel]').forEach(carousel => {
                const items = carousel.querySelectorAll('.carousel-item');
                const indicators = carousel.querySelectorAll('.carousel-indicators button');
                const prev = carousel.querySelector('[data-slide="prev"]');
                const next = carousel.querySelector('[data-slide="next"]');
                let currentIndex = 0;
    
                const updateCarousel = (index) => {
                    items.forEach((item, i) => item.classList.toggle('active', i === index));
                    indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
                };
    
                indicators.forEach((btn, i) => {
                    btn.addEventListener('click', () => {
                        currentIndex = i;
                        updateCarousel(currentIndex);
                    });
                });
    
                prev?.addEventListener('click', () => {
                    currentIndex = (currentIndex - 1 + items.length) % items.length;
                    updateCarousel(currentIndex);
                });
    
                next?.addEventListener('click', () => {
                    currentIndex = (currentIndex + 1) % items.length;
                    updateCarousel(currentIndex);
                });
                // ride functionality
                if (3 > 4) {// ride check data-carousel-ride?
                    let interval = setInterval(() => {
                        let nextIndex = (currentIndex + 1) % items.length;
                        goToSlide(nextIndex);
                    }, 5000); // 5s interval
                }
            });
        });
        
    </script>
@endonce
@if(config('media-library-extensions.youtube_support_enabled'))
    <x-mle-partial-assets include-css="true" include-js="true" include-youtube-iframe-api="true"/>
@else
    <x-mle-partial-assets include-css="true" include-js="true"/>
@endif


{{--<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>--}}
{{--    <div id="{{ $id }}"--}}
{{--         class="media-carousel plain-carousel mle-width-100 mle-height-100"--}}
{{--         data-carousel--}}
{{--         data-carousel-id="{{ $id }}">--}}

{{--        --}}{{-- Indicators --}}
{{--        <div--}}
{{--            class="media-carousel-indicators carousel-indicators {{ $mediaItems->count() < 2 ? 'mle-display-none' : '' }}">--}}
{{--            @foreach($mediaItems as $index => $medium)--}}
{{--                <button--}}
{{--                    type="button"--}}
{{--                    data-slide-to="{{ $index }}"--}}
{{--                    class="{{ $loop->first ? 'active' : '' }}"--}}
{{--                    aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}">--}}
{{--                </button>--}}
{{--            @endforeach--}}
{{--        </div>--}}

{{--        --}}{{-- Slides --}}
{{--        <div class="media-carousel-inner carousel-inner">--}}
{{--            @foreach($mediaItems as $index => $medium)--}}
{{--                <div class="carousel-item {{ $loop->first ? 'active' : '' }} {{ $clickToOpenInModal ? 'mle-cursor-zoom-in' : '' }}">--}}
{{--                    <div class="carousel-item-wrapper d-flex align-items-center justify-content-center"--}}
{{--                         @if($clickToOpenInModal)--}}
{{--                             data-open-modal="#{{ $id }}-modal"--}}
{{--                        @endif>--}}
{{--                        @if($medium->hasCustomProperty('youtube-id'))--}}
{{--                            <x-mle-video-youtube--}}
{{--                                class="mle-video-responsive"--}}
{{--                                :medium="$medium"--}}
{{--                                :preview="$inModal ? false : true"--}}
{{--                                :youtube-id="$medium->getCustomProperty('youtube-id')"--}}
{{--                                :youtube-params="$inModal ? ['mute' => 1] : []"--}}
{{--                            />--}}
{{--                        @else--}}
{{--                            <x-mle-image-responsive--}}
{{--                                class="mle-image-responsive"--}}
{{--                                :medium="$medium"--}}
{{--                                :conversions="['16x9']"--}}
{{--                                sizes="100vw"--}}
{{--                                :alt="$medium->name"--}}
{{--                            />--}}
{{--                        @endif--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}

{{--        --}}{{-- Prev/Next Controls --}}
{{--        <button--}}
{{--            class="media-carousel-control-prev {{ count($mediaItems) <= 1 ? 'disabled' : '' }}"--}}
{{--            type="button"--}}
{{--            data-carousel-prev--}}
{{--            title="{{ __('media-library-extensions::messages.previous') }}">--}}
{{--            <span class="carousel-control-prev-icon" aria-hidden="true"></span>--}}
{{--            <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>--}}
{{--        </button>--}}
{{--        <button--}}
{{--            class="media-carousel-control-next {{ count($mediaItems) <= 1 ? 'disabled' : '' }}"--}}
{{--            type="button"--}}
{{--            data-carousel-next--}}
{{--            title="{{ __('media-library-extensions::messages.next') }}">--}}
{{--            <span class="carousel-control-next-icon" aria-hidden="true"></span>--}}
{{--            <span class="visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>--}}
{{--        </button>--}}
{{--    </div>--}}

{{--    @if($clickToOpenInModal)--}}
{{--        <x-mle-media-modal--}}
{{--            :id="$id"--}}
{{--            :model="$model"--}}
{{--            :media-collection="$mediaCollection"--}}
{{--            :media-collections="$mediaCollections"--}}
{{--            title="Media carousel"/>--}}
{{--    @endif--}}
{{--</div>--}}

{{--@once--}}
{{--    <style>--}}
{{--        .plain-carousel {--}}
{{--            position: relative;--}}
{{--            overflow: hidden;--}}
{{--        }--}}
{{--        .plain-carousel .carousel-inner {--}}
{{--            position: relative;--}}
{{--            width: 100%;--}}
{{--            height: 100%;--}}
{{--        }--}}
{{--        .plain-carousel .carousel-item {--}}
{{--            position: absolute;--}}
{{--            opacity: 0;--}}
{{--            transition: opacity 0.5s ease-in-out;--}}
{{--            width: 100%;--}}
{{--            height: 100%;--}}
{{--        }--}}
{{--        .plain-carousel .carousel-item.active {--}}
{{--            position: relative;--}}
{{--            opacity: 1;--}}
{{--            z-index: 1;--}}
{{--        }--}}
{{--        .plain-carousel .carousel-indicators button.active {--}}
{{--            background-color: #000;--}}
{{--        }--}}
{{--    </style>--}}

{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            document.querySelectorAll('[data-carousel]').forEach(carousel => {--}}
{{--                const id = carousel.getAttribute('data-carousel-id');--}}
{{--                const items = Array.from(carousel.querySelectorAll('.carousel-item'));--}}
{{--                const indicators = carousel.querySelectorAll('[data-slide-to]');--}}
{{--                let currentIndex = items.findIndex(i => i.classList.contains('active'));--}}
{{--                if (currentIndex === -1) currentIndex = 0;--}}

{{--                const goToSlide = (index) => {--}}
{{--                    items.forEach((item, i) => {--}}
{{--                        item.classList.toggle('active', i === index);--}}
{{--                    });--}}
{{--                    indicators.forEach((btn, i) => {--}}
{{--                        btn.classList.toggle('active', i === index);--}}
{{--                    });--}}
{{--                    currentIndex = index;--}}
{{--                };--}}

{{--                carousel.querySelectorAll('[data-carousel-next]').forEach(btn => {--}}
{{--                    btn.addEventListener('click', () => {--}}
{{--                        let nextIndex = (currentIndex + 1) % items.length;--}}
{{--                        goToSlide(nextIndex);--}}
{{--                    });--}}
{{--                });--}}

{{--                carousel.querySelectorAll('[data-carousel-prev]').forEach(btn => {--}}
{{--                    btn.addEventListener('click', () => {--}}
{{--                        let prevIndex = (currentIndex - 1 + items.length) % items.length;--}}
{{--                        goToSlide(prevIndex);--}}
{{--                    });--}}
{{--                });--}}

{{--                indicators.forEach((btn, i) => {--}}
{{--                    btn.addEventListener('click', () => {--}}
{{--                        goToSlide(i);--}}
{{--                    });--}}
{{--                });--}}

{{--                // Optional modal trigger--}}
{{--                carousel.querySelectorAll('[data-open-modal]').forEach(el => {--}}
{{--                    el.addEventListener('click', (e) => {--}}
{{--                        const target = el.getAttribute('data-open-modal');--}}
{{--                        if (target) {--}}
{{--                            document.querySelector(target)?.classList.add('is-visible');--}}
{{--                        }--}}
{{--                    });--}}
{{--                });--}}
{{--            });--}}

{{--            // ride functionality--}}
{{--            if (3 > 4) {// ride check data-carousel-ride?--}}
{{--                let interval = setInterval(() => {--}}
{{--                    let nextIndex = (currentIndex + 1) % items.length;--}}
{{--                    goToSlide(nextIndex);--}}
{{--                }, 5000); // 5s interval--}}
{{--            }--}}

{{--        });--}}
{{--    </script>--}}
{{--@endonce--}}