@if(!$iconExists && config('media-library-extensions.debug'))
    <div class="media-manager-debug">
        <div role="img" aria-label="{{ __('media-library-extensions::messages.warning') }}" title="{{ __('media-library-extensions::messages.warning') }}">⚠️</div>
        <ul>
        @foreach($errors as $error)
           <li>
               {!! $error !!}
           </li>
        @endforeach
        </ul>
    </div>
@endif
