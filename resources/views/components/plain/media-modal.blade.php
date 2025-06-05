<div class="mlbrgn-mle-component">
    <div
        {{ $attributes->merge(['class' => "media-modal $sizeClass"]) }}
        id="{{ $id }}"
        tabindex="-1"
        role="dialog"
        aria-labelledby="{{ $id }}-title"
        aria-hidden="true"
        @if($videoAutoPlay)
            data-video-autoplay=""
        @endif
    >
        <div class="modal-dialog">
            <div class="modal-content justify-content-center">
                <h1 class="modal-title visually-hidden" id="{{ $id }}-title">{{ $title }}</h1>
                <div class="modal-body p-0">
                    <button
                        type="button"
                        class="btn-close"
                        data-modal-close
                        aria-label="Sluit"
                        title="{{ __('media-library-extensions::messages.close') }}"></button>

                    <x-mle-media-carousel
                        class="mle-width-100 mle-height-100"
                        id="{{ $id }}"
                        :model="$model"
                        :click-to-open-in-modal="false"
                        :media-collection="$mediaCollection"
                        :media-collections="$mediaCollections"
                        :in-modal="true"/>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .media-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        display: none;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.75);
        z-index: 1050;
        overflow: hidden;
    }

    .media-modal.active {
        display: flex;
    }

    .media-modal .modal-dialog {
        width: 90vw;
        max-width: 1000px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .media-modal .modal-content {
        background: white;
        border-radius: 0.5rem;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .media-modal .modal-body {
        flex-grow: 1;
        overflow: hidden;
        position: relative;
    }

    .btn-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const openModal = (modalId) => {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = (modal) => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        };

        // Attach to all close buttons
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const modal = btn.closest('.media-modal');
                if (modal) closeModal(modal);
            });
        });

        // Optional: clicking outside modal-content also closes
        document.querySelectorAll('.media-modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
            });
        });

        // Optional: add triggers
        document.querySelectorAll('[data-modal-trigger]').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-modal-trigger');
                openModal(target);
            });
        });
    });
    // example trigger button
    {{--<button data-modal-trigger="{{ $id }}">Open Modal</button>--}}

</script>
<x-mle-partial-assets include-css="true" include-js="true"/>


