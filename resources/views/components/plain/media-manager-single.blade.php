<div class="mlbrgn-mle-component">
    <div id="{{ $id }}" {{ $attributes->class([
            'media-manager media-manager-single',
        ]) }}>
        <x-mle-partial-debug/>

        <div class="media-manager-row media-manager-single-row row">
            <div class="media-manager-form col-12 col-md-4">
                @if($uploadEnabled)
                    <x-mle-partial-upload-form
                        :allowedMimeTypes="$allowedMimeTypes"
                        :mediaCollection="$mediaCollection"
                        :model="$model"
                        :id="$id"
                        :multiple="false"/>
                @endif
            </div>

            <div class="media-manager-preview-wrapper media-manager-single-preview-wrapper col-12 col-md-8 text-center">
                @if($medium)
                    <a
                        class="media-manager-preview-medium-link media-manager-single-preview-medium-link mle-cursor-zoom-in"
                        data-modal-id="{{ $id }}-modal"
                        data-slide-to="0"
                        onclick="openModalSlide(this)">
                        <img
                            src="{{ $medium->getUrl() }}"
                            class="media-manager-preview-medium media-manager-single-preview-medium image-fluid"
                            alt="{{ __('media-library-extensions::messages.no_medium') }}">
                    </a>
                    <div class="media-manager-preview-menu media-manager-single-preview-menu">
                        @if($destroyEnabled)
                            <x-mle-partial-destroy-form :medium="$medium" :id="$id"/>
                        @endif
                    </div>

                    {{-- JS-Driven Modal --}}
                    <x-mle-media-modal
                        :id="$id"
                        :model="$model"
                        :media-collection="$mediaCollection"
                        :media="collect([$medium])"
                        :inModal="true"
                        :plainJs="true"
                        title="Media carousel"/>
                @else
                    <span>{{ __('media-library-extensions::messages.no_medium') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

@once
{{--    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">--}}
    <script>
        function openModalSlide(el) {
            const modalId = el.getAttribute('data-modal-id');
            const slideTo = parseInt(el.getAttribute('data-slide-to'), 10);
            const modal = document.getElementById(modalId);
            const slides = modal.querySelectorAll('.custom-carousel-slide');

            slides.forEach((s, i) => s.classList.toggle('active', i === slideTo));
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        function nextSlide(modalId) {
            const modal = document.getElementById(modalId);
            const slides = modal.querySelectorAll('.custom-carousel-slide');
            let current = Array.from(slides).findIndex(s => s.classList.contains('active'));
            slides[current].classList.remove('active');
            slides[(current + 1) % slides.length].classList.add('active');
        }

        function prevSlide(modalId) {
            const modal = document.getElementById(modalId);
            const slides = modal.querySelectorAll('.custom-carousel-slide');
            let current = Array.from(slides).findIndex(s => s.classList.contains('active'));
            slides[current].classList.remove('active');
            slides[(current - 1 + slides.length) % slides.length].classList.add('active');
        }
    </script>
@endonce
<x-mle-partial-assets include-css="true" />
