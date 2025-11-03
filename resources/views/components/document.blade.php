<div {{ $attributes->class('mle-document') }} mle-document>
    <div class="mle-document-preview">
        <x-mle-shared-icon
            class="mle-document-bg-icon"
            :name="$icon['name']"
            :title="$icon['title']"
        />
        <div class="mle-document-info">
            <p>
                {{ Str::limit($medium->file_name, 15) }}
            </p>
            <p>
                {{ mle_human_filesize($medium->size, 0) }}
            </p>

            {{-- Download link (only if not preview) --}}
            @if(!$previewMode)
                <a href="{{ $medium->getUrl() }}" target="_blank" class="mle-document-link" data-mle-document-link>
                    <x-mle-shared-icon
                        class="mle-document-fg-icon"
                        :name="$icon['name']"
                        :title="$icon['title']"
                    />
                    {{ __('media-library-extensions::messages.download_document') }}
                </a>
            @else
                <x-mle-shared-icon
                    class="mle-document-fg-icon"
                    :name="$icon['name']"
                    :title="$icon['title']"
                />
            @endif

            @if(!$previewMode)
                @if($medium->mime_type === 'application/pdf' && config('media-library-extensions.preview_modal_embed_pdf'))
                    <embed src="{{ $medium->getUrl() }}" type="application/pdf"
                           width="100%" height="600px" class="mle-document-embed" />
                @elseif(in_array($medium->mime_type, $officeMimes, true))
                    @if(config('media-library-extensions.use_external_document_viewer') === 'google-docs')
                        <iframe src="https://docs.google.com/gview?url={{ urlencode($medium->getUrl()) }}&embedded=true"
                                style="width:100%; height:600px;" frameborder="0" class="mle-document-embed"></iframe>
                    @elseif(config('media-library-extensions.use_external_document_viewer') === 'microsoft-office')
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($medium->getUrl()) }}"
                                style="width:100%; height:600px;" frameborder="0" class="mle-document-embed"></iframe>
                    @endif
                @endif
            @endif
        </div>
    </div>
</div>
