// noinspection JSUnresolvedReference
import {getMleBootstrapInstance} from "@/js/bootstrap-6/bootstrap-resolver";

let ytlPlayers = {}; // Store YouTube-lite players
let nativeMediaPlayers = {};// store native media players (audio / video)

const closeBootstrapDialog = (dialog) => {
    // try to find a close button that uses data-bs-dismiss="dialog" first
    // as it's more reliable in some environments than the JS API
    const closeButton = dialog.querySelector('[data-bs-dismiss="dialog"]');
    if (closeButton) {
        closeButton.click();
    }

    const bs = getMleBootstrapInstance();
    const dialogInstance = bs.Dialog.getOrCreateInstance(dialog);
    dialogInstance.hide();

    // Safety fallback for tests/race conditions: if it's still visible after a tiny tick, force it.
    // We use a small timeout to allow Bootstrap's native event cycle a chance to run first.
    setTimeout(() => {
        if (dialog.classList.contains('show') || dialog.hasAttribute('open')) {
            // console.warn('Forcing dialog closed after hide() failed');
            dialog.classList.remove('show');
            dialog.style.display = 'none';
            if (typeof dialog.close === 'function') {
                try {
                    dialog.close();
                } catch (e) {
                    // console.warn('dialog.close() failed', e);
                }
            }
            document.body.classList.remove('dialog-open');
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            const backdrop = document.querySelector('.dialog-backdrop, .modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }, 10);
}

const initializeMediaModal = function (modal) {
    if (modal.dataset.mleMediaModalInitialized) return;

    const bs = getMleBootstrapInstance();

    const carousel = modal.querySelector('[data-mle-carousel]');
    const modalId = modal.id;

    function setupYT (videoSlide) {
        const youTubeId = videoSlide.getAttribute('data-mle-youtube-video-id');
        const playerId = modalId + '-' + youTubeId;
        const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
        // instantiate the new player instance for slide
        ytlPlayers[playerId] = new YT.Player(iframe, {
            events: {
                onReady: () => {
                    ytlPlayers[playerId].isReady = true;
                },
            },
        });
    }

    function setupNativeMedia(slide) {
        const audio = slide.querySelector('audio');
        const video = slide.querySelector('video');

        let nativeMediaPlayerId = null;

        // audio an video use the medium id,
        // so on refresh just overwrite the nativeMediaPlayer registration
        if (audio) {
            nativeMediaPlayerId = audio.id;
            nativeMediaPlayers[nativeMediaPlayerId] = audio;
        }
        if (video) {
            nativeMediaPlayerId = video.id;
            nativeMediaPlayers[nativeMediaPlayerId] = video;
        }

        return nativeMediaPlayerId;
    }

    function controlNativeMedia(nativeMediaPlayerId, action) {
        const media = nativeMediaPlayers[nativeMediaPlayerId];
        if (!media) return;

        try {
            if (action === 'play') media.play();
            else if (action === 'pause') media.pause();
        } catch (err) {
            console.warn(`Failed to ${action} media:`, err);
        }
    }

    // Event listener for when the YouTube iframe loads
    modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
        const targetSlide = event.target.closest('[data-mle-youtube-video-id]');
        if (targetSlide) setupYT(targetSlide);
    });

    // when the carousel opens on the first slide and this is a YT video an audio or a video element,
    // we need to start autoplaying on modal open.
    modal.addEventListener('shown.bs.dialog', (e) => {
        const modalTrigger = e.detail?.relatedTarget || e.relatedTarget;
        if (!modalTrigger) return;

        const slideTo = modalTrigger.getAttribute('data-bs-slide-to');
        const carouselElement = modal.querySelector('[data-mle-carousel]');

        if (slideTo !== null && carouselElement) {
            const bs = getMleBootstrapInstance();
            const carouselInstance = bs.Carousel.getOrCreateInstance(carouselElement);
            // No need for timeout here as dialog is already shown
            carouselInstance.to(parseInt(slideTo, 10));
        }
    });

    modal.addEventListener('shown.bs.dialog', (e) => {
        // nothing to do, return
        if (!modal.hasAttribute('data-mle-autoplay')) return;

        const modalTrigger = e.detail?.relatedTarget || e.relatedTarget;
        if (!modalTrigger) return;

        const slideTo = modalTrigger.getAttribute('data-bs-slide-to');
        if (slideTo !== '0' && slideTo !== null) return;

        const firstSlide = carousel.querySelector('[data-mle-carousel-item]:first-child');
        // console.log('firstSlide', firstSlide);
        if (!firstSlide) return;

        const nativeMediaPlayerId = setupNativeMedia(firstSlide);
        controlNativeMedia(nativeMediaPlayerId, 'play');

        const youtubeVideoContainer = firstSlide.querySelector('[data-mle-youtube-video]');
        if (youtubeVideoContainer) {
            const youTubeId = youtubeVideoContainer.getAttribute('data-mle-youtube-video-id');
            if (!youTubeId) return;

            const playerId = `${modalId}-${youTubeId}`;
            controlYouTubePlayback(playerId, 'playVideo');
        }
    });

    // Stop the video when the modal is hidden
    modal.addEventListener('hidden.bs.dialog', () => {

        const carouselElement = modal.querySelector('[data-mle-carousel]');
        if (!carouselElement) return;

        const bs = getMleBootstrapInstance();
        const carouselInstance = bs.Carousel.getInstance(carouselElement);
        if (!carouselInstance) return;

        stopAllMediaPlayBack();

        carousel.removeEventListener('slide.bs.carousel', slideEventListener);
        carouselInstance.to(0)
        carousel.addEventListener('slide.bs.carousel', slideEventListener);
    });

    const slideEventListener = (event) => {

        if (!modal.hasAttribute('data-mle-autoplay')) return;

        pauseAllMediaPlayBack()

        const slide = event.relatedTarget;
        if (!slide) return;

        const nativeMediaPlayerId = setupNativeMedia(slide);
        controlNativeMedia(nativeMediaPlayerId, 'play');

        const ytContainer = slide.querySelector('[data-mle-youtube-video]');
        if (ytContainer && ytContainer.hasAttribute('data-mle-youtube-video-id')) {
            let youTubeId = ytContainer.getAttribute('data-mle-youtube-video-id');
            const playerId = modalId + '-' + youTubeId;

            controlYouTubePlayback(playerId, 'playVideo');
        }
    }

    // Handle carousel sliding
    carousel.addEventListener('slide.bs.carousel', slideEventListener);

    modal.addEventListener('keydown', (e) => {
        // Dialogs don't use 'show' class anymore, they use open attribute
        if (!modal.open) return;

        const carouselElement = modal.querySelector('[data-mle-carousel]');
        if (!carouselElement) return;

        const carouselInstance = bs.Carousel.getInstance(carouselElement);
        if (!carouselInstance) return;

        const isInsideCarousel = carousel.contains(e.target);

        if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) {
            return; // let the carousel's listener handle it
        }

        // If outside the carousel (e.g., focus on close button), we handle the keys here
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            e.stopPropagation();
            carouselInstance.prev();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            e.stopPropagation();
            carouselInstance.next();
        } else if (e.key === 'Escape') {
            closeBootstrapDialog(modal);
        }
    });

    // Mark as initialized
    modal.dataset.mleMediaModalInitialized = 'true';

    function controlYouTubePlayback(playerId, action = 'playVideo', attempt = 0, maxAttempts = 10, timeOut = 200) {
        const actionsMap = {
            playVideo: 'playVideo',
            pauseVideo: 'pauseVideo',
            stopVideo: 'stopVideo'
        };

        const method = actionsMap[action];
        if (!actionsMap[action]) {
            console.warn(`Unknown action: ${action}`);
            return;
        }

        const player = ytlPlayers[playerId];

        if (!player || !player.isReady) {
            // console.log('No player, or player not ready yet', playerId, 'player', player, 'playerIsReady', player?.isReady);
            if (attempt < maxAttempts) {
                setTimeout(() => controlYouTubePlayback(playerId, action, attempt + 1, maxAttempts, timeOut * 1.2), timeOut);
            }
            return;
        }

        if (method) player[method]();
    }

    function pauseAllMediaPlayBack() {
        Object.keys(ytlPlayers).forEach((playerId) => controlYouTubePlayback(playerId, 'pauseVideo'));
        Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
    }

    function stopAllMediaPlayBack() {
        // console.log('stopAllMediaPlayBack');
        Object.keys(ytlPlayers).forEach((playerId) => controlYouTubePlayback(playerId, 'stopVideo'));
        Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
    }
}

// listen to preview updated to reinitialize functionality
document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
    // console.log('bootstrap-6/modal-media.js - mediaManagerPreviewsUpdated received');
    ytlPlayers = {};
    nativeMediaPlayers = {};
    const mediaManager = e.detail.mediaManager;
    mediaManager.querySelectorAll('[data-mle-media-modal]').forEach(initializeMediaModal);
});

document.querySelectorAll('[data-mle-media-modal]').forEach(initializeMediaModal);
