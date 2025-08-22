import {
    initModalEvents,
    openModal as baseOpenModal,
    registerModalEventHandler,
    reinitModalEvents,
    setupModalBase
} from './modal-core';

import { getCarouselController } from '@/js/plain/media-carousel';

document.addEventListener('DOMContentLoaded', () => {

    let ytlPlayers = {}; // key: youtubeId, value: YT.Player instance
    let nativeMediaPlayers = {};// store native media players (audio / video)

    function initializeMediaModal(modal) {
        const carousel = modal.querySelector('[data-carousel]');
        setupModalBase(modal);

        modal.addEventListener('mleModalOpened', (e) => {
            const trigger = e.detail.trigger;
            if (!trigger) return;
            const modal = e.detail.modal;
            if (!modal) return;
            const modalId = modal.id;

            const autoPlay = modal.hasAttribute('data-autoplay');
            if (!autoPlay) return;

            const carousel = modal.querySelector('[data-carousel]');
            if (!carousel) return;

            const firstSlide = carousel.querySelector('.media-carousel-item:first-child');
            if (!firstSlide) return;

            // TODO need the slideTo index to know if i should start playing
            const slideTo = parseInt(trigger.getAttribute('data-slide-to') || '0', 10);

            if (slideTo === 0) {
                const nativeMediaPlayerId = setupNativeMedia(firstSlide);
                controlNativeMedia(nativeMediaPlayerId, 'play');

                const ytContainer = firstSlide.querySelector('[data-mle-youtube-video]');
                if (ytContainer) {
                    const youTubeId = ytContainer.getAttribute('data-youtube-video-id');
                    const playerId = `${modalId}-${youTubeId}`
                    if (youTubeId) controlYouTubePlayback(playerId, 'playVideo');
                }
            }
        });

        modal.addEventListener('mleModalClosed', (e) => {
            stopAllMediaPlayBack()
            const modal = e.detail.modal;
            const controller = getCarouselController(modal.querySelector('[data-carousel]'));
            if (!controller) return;
            // go back to slide 0
            // otherwise slide event won't get triggered when going to the same slide after closing modal
            controller.goToSlide(0, true, true);

            stopAllMediaPlayBack();

        });

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

        carousel?.addEventListener('mleCarouselSlided', (e) => {
            const carousel = e.detail.carousel;
            const currentSlide = e.detail.currentSlide;
            const modal = carousel.closest('[data-modal]');
            const autoPlay = modal.hasAttribute('data-autoplay');
            if (!autoPlay) return;

            pauseAllMediaPlayBack();

            if (!autoPlay) return;

            const nativeMediaPlayerId = setupNativeMedia(currentSlide);
            controlNativeMedia(nativeMediaPlayerId, 'play');

            // const audio = currentSlide.querySelector('[data-mle-audio]');
            // const video = currentSlide.querySelector('[data-mle-video]');
            //

            // audio?.play().catch(err => console.warn('Audio autoplay failed:', err));
            // video?.play().catch(err => console.warn('Video autoplay failed:', err));
            const ytContainer = currentSlide.querySelector('[data-mle-youtube-video]');

            if (ytContainer) {
                const modalId = modal.id;
                const youTubeId = ytContainer.getAttribute('data-youtube-video-id');
                const playerId = modalId + '-' + youTubeId
                controlYouTubePlayback(playerId, 'playVideo');
            }
        });

        function controlNativeMedia(playerId, action) {
            const media = nativeMediaPlayers[playerId];
            if (!media) return;
            try {
                if (action === 'play') media.play();
                else if (action === 'pause') media.pause();
            } catch (err) {
                console.warn(`Failed to ${action} media:`, err);
            }
        }

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
            Object.keys(ytlPlayers).forEach(id => controlYouTubePlayback(id, 'pauseVideo'));
            Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
        }

        function stopAllMediaPlayBack() {
            Object.keys(ytlPlayers).forEach(id => controlYouTubePlayback(id, 'stopVideo'));
            Object.keys(nativeMediaPlayers).forEach(id => controlNativeMedia(id, 'pause'));
        }

    }

    function initMediaModalEvents() {
        initModalEvents(); // base modal interaction
    }

    function mediaModalKeydownHandler(e) {
        const modal = e.target.closest('[data-media-modal].active');
        if (!modal) return;

        const controller = getCarouselController(modal.querySelector('[data-carousel]'));
        if (!controller) return;

        const isInsideCarousel = modal.querySelector('[data-carousel]').contains(e.target);

        if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) return;

        if (e.key === 'ArrowLeft') {
            controller.goToPreviousSlide();
        } else if (e.key === 'ArrowRight') {
            controller.goToNextSlide();
        }
    }

    // extra functionality that needs to be performed when modal is clicked?
    function mediaModalClickHandler(e) {
        const trigger = e.target.closest('[data-modal-trigger]');
        if (!trigger) return;

        const modalId = trigger.getAttribute('data-modal-trigger');
        const slideTo = parseInt(trigger.getAttribute('data-slide-to') || '0', 10);
        const modal = document.querySelector(modalId);
        const carousel = modal?.querySelector('[data-carousel]');
        const controller = getCarouselController(carousel);

        if (controller) {
            controller.goToSlide(slideTo, true);
        }
    }

    function liteYoutubeHandler(e) {
        const youTubeVideoContainer = e.target.closest('[data-youtube-video-id]');
        if (!youTubeVideoContainer) return;

        const modal = youTubeVideoContainer.closest('[data-modal]');
        if (!modal) return;

        const modalId = modal.id;

        const youTubeId = youTubeVideoContainer.getAttribute('data-youtube-video-id');
        if (!youTubeId) return;

        const playerId = modalId + '-' + youTubeId;
        if (ytlPlayers[playerId]) return;

        const iframe = youTubeVideoContainer.querySelector('lite-youtube')?.shadowRoot?.querySelector('iframe');
        if (!iframe) return;

        ytlPlayers[playerId] = new YT.Player(iframe, {
            events: { onReady: () => ytlPlayers[playerId].isReady = true }
        });
    }

    registerModalEventHandler('click', mediaModalClickHandler);
    registerModalEventHandler('keydown', mediaModalKeydownHandler);

    // global, custom event
    document.addEventListener('liteYoutubeIframeLoaded', liteYoutubeHandler);

    // initial init
    initMediaModalEvents();
    document.querySelectorAll('[data-media-modal]').forEach(initializeMediaModal);

    // reinit on update of previews
    document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
        ytlPlayers = {};
        nativeMediaPlayers = {};
        const mediaManager = e.detail.mediaManager;

        reinitModalEvents();
        registerModalEventHandler('click', mediaModalClickHandler);
        registerModalEventHandler('keydown', mediaModalKeydownHandler);

        mediaManager.querySelectorAll('[data-media-modal]').forEach(initializeMediaModal);
    });
});
