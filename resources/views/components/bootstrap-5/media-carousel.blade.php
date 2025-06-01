<div {{ $attributes->merge(['class' => 'mlbrgn-mle-component']) }}>
    <div id="{{ $id }}"
         @class([
             'media-carousel', 
             'carousel', 
             'slide',
             'carousel-fade' => config('media-library-extensions.carousel_fade')       
          ])
        @if(config('media-library-extensions.carousel_ride'))
            data-bs-ride="{{ config('media-library-extensions.carousel_ride_only_after_interaction') ? 'true' : 'carousel' }}"
            data-bs-interval="{{ config('media-library-extensions.carousel_ride_interval') }}"
        @endif
    >
        <div 
            @class([
                'media-carousel-indicators', 
                'carousel-indicators', 
                'mle-display-none' => $mediaItems->count() < 2
            ])>
            @foreach($mediaItems as $index => $medium)
                <button
                    type="button"
                    data-bs-target="#{{ $id }}"
                    data-bs-slide-to="{{ $index }}"
                    @class(['active' => $loop->first])
                    @if($loop->first) aria-current="true" @endif
                    aria-label="{{ __('media-library-extensions::messages.slide_to_:index', ['index' => $index + 1]) }}"></button>
            @endforeach
        </div>
    
        <div class="media-carousel-inner carousel-inner">
            @foreach($mediaItems as $index => $medium)
                <div class="carousel-item @if($loop->first) active @endif">
                    <div class="carousel-item-wrapper d-flex align-items-center justify-content-center" 
                         data-bs-toggle="modal"
                         data-bs-target="#{{$id}}-modal"
                    >
                    {{-- TODO which conversions?--}}
                    <x-mle-image-responsive
                        class="d-block w-100"
                        :medium="$medium"
                        :conversions="['thumb', '16x9']"
                        sizes="100vw"
                        :alt="$medium->name"
                        data-bs-target="#{{$id}}-modal-carousel"
                        data-bs-slide-to="{{ $loop->index }}"
                    />
                    </div>
                </div>
            @endforeach
        </div>
    
        <button
            @class([
                 'media-carousel-control-prev',
                 'carousel-control-prev',
                 'disabled' => count($mediaItems) <= 1
             ])
            type="button" data-bs-target="#{{ $id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.previous') }}</span>
        </button>
        <button
            @class([
                'media-carousel-control-next',
                'carousel-control-next',
                'disabled' => count($mediaItems) <= 1
            ])
            type="button" data-bs-target="#{{ $id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('media-library-extensions::messages.next') }}</span>
        </button>
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
    <link rel="stylesheet" href="{{ asset('vendor/media-library-extensions/app.css') }}">
    <script src="{{ asset('vendor/media-library-extensions/app.js') }}"></script>
    <script src="https://www.youtube.com/iframe_api"></script>
@endonce

