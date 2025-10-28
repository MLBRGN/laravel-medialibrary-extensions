<div class="media-preview-grid" data-media-preview-grid>
    <x-mle-lab-preview-original :medium="$medium" />
    <x-mle-lab-preview-base :medium="$medium" />
</div>

{{--    <x-mle-lab-preview--}}
{{--        class="mle-media-lab-conversions"--}}
{{--        title="{{ __('media-library-extensions::messages.conversion') }}"--}}
{{--        :model-or-class-name="$medium->model"--}}
{{--    >--}}
{{--        @foreach(array_keys($medium->extra_meta['conversions']) as $conversionName)--}}
{{--            <x-mle-image-responsive--}}
{{--                :medium="$medium"--}}
{{--                :conversions="[$conversionName]"--}}
{{--                class="mx-auto media-preview-image"--}}
{{--            />--}}
{{--        @endforeach--}}
{{--    </x-mle-lab-preview>--}}