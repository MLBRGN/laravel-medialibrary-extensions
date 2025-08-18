import {
    initModalEvents,
    openModal as baseOpenModal,
    registerModalEventHandler,
    reinitModalEvents,
    setupModalBase
} from './modal-core';

import { getCarouselController } from '@/js/plain/media-carousel';

document.addEventListener('DOMContentLoaded', () => {

    let players = {}; // key: youtubeId, value: YT.Player instance

    function setupMediaModal(modal) {
        const carousel = modal.querySelector('[data-carousel]');
        const autoPlay = modal.hasAttribute('data-video-autoplay');
        const modalId = modal.id;

        console.log('autoPlay', autoPlay);
        setupModalBase(modal, stopAllVideoPlayBack, () => {
            if (!autoPlay) return;

            const activeSlide = modal.querySelector('.media-carousel-item.active');
            const videoWrapper = activeSlide?.querySelector('.media-video-container[data-youtube-video-id]');
            console.log('activeSlide', activeSlide);
            console.log('videoWrapper', videoWrapper);
            if (videoWrapper) {
                console.log('startVideoPlayBack');

                const modalId = modal.id;
                const youTubeId = videoWrapper.getAttribute('data-youtube-video-id');
                const playerId = modalId + '-' + youTubeId
                startVideoPlayBack(playerId);
            }
        });

        carousel?.addEventListener('carouselSlided', () => {
            console.log('carouselSlided, pause videos');
            pauseAllVideoPlayBack();

            console.log('autoPlay');
            if (!autoPlay) return;

            const activeSlide = carousel.querySelector('.media-carousel-item.active');
            const videoWrapper = activeSlide?.querySelector('.media-video-container[data-youtube-video-id]');
            if (videoWrapper) {
                console.log('startVideoPlayBack');

                const modalId = modal.id;
                const youTubeId = videoWrapper.getAttribute('data-youtube-video-id');
                const playerId = modalId + '-' + youTubeId
                startVideoPlayBack(playerId);
            }
        });

        function controlVideoPlayback(playerId, action = 'playVideo', attempt = 0, maxAttempts = 10, timeOut = 200) {
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

            const player = players[playerId];

            if (!player || !player.isReady) {
                console.log('No player, or player not ready yet', playerId, 'player', player, 'playerIsReady', player?.isReady);
                if (attempt < maxAttempts) {
                    setTimeout(() => controlVideoPlayback(playerId, action, attempt + 1, maxAttempts, timeOut * 1.2), timeOut);
                }
                return;
            }

            if (method) player[method]();
        }

        function pauseVideoPlayBack(playerId, attempt = 0, maxAttempts = 10, timeOut = 200) {
            controlVideoPlayback(playerId, 'pauseVideo', attempt, maxAttempts, timeOut);
        }

        function stopVideoPlayBack(playerId, attempt = 0, maxAttempts = 10, timeOut = 200) {
            controlVideoPlayback(playerId, 'stopVideo', attempt, maxAttempts, timeOut);
        }

        function startVideoPlayBack(playerId, attempt = 0, maxAttempts = 10, timeOut = 200) {
            controlVideoPlayback(playerId, 'playVideo', attempt, maxAttempts, timeOut);
        }

        function pauseAllVideoPlayBack() {
            Object.keys(players).forEach((playerId) => pauseVideoPlayBack(playerId, 0, 10, 200));
        }

        function stopAllVideoPlayBack() {
            Object.keys(players).forEach((playerId) => stopVideoPlayBack(playerId, 0, 10, 200));
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

    function mediaModalClickHandler(e) {
        const trigger = e.target.closest('[data-modal-trigger]');
        if (!trigger) return;

        const modalId = trigger.getAttribute('data-modal-trigger');
        const slideTo = parseInt(trigger.getAttribute('data-slide-to') || '0', 10);

        console.log('slideTo', slideTo);
        baseOpenModal(modalId);

        const modal = document.querySelector(modalId);
        const carousel = modal?.querySelector('[data-carousel]');
        console.log('modal', modal);
        console.log('carousel', carousel);
        // console.log('carousels', carousels);
        const controller = getCarouselController(carousel);
        console.log('controller', controller);
        if (controller) {
            controller.goToSlide(slideTo, true);
        }
    }

    function liteYoutubeHandler(e) {
        const modal = e.target.closest('[data-media-modal]');
        if (!modal) return;

        const modalId = modal.id;
        const videoSlide = e.target.closest('[data-youtube-video-id]');
        const youTubeId = videoSlide?.getAttribute('data-youtube-video-id');
        if (!youTubeId) return;

        const playerId = modalId + '-' + youTubeId;
        if (players[playerId]) return; // already initialized

        const liteYoutube = videoSlide.querySelector('lite-youtube');
        const iframe = liteYoutube?.shadowRoot?.querySelector('iframe');
        if (!iframe) return;

        players[playerId] = new YT.Player(iframe, {
            events: {
                onReady: (event) => {
                    console.log('player ready', playerId);
                    players[playerId].isReady = true;
                },
            },
        });
    }

    registerModalEventHandler('click', mediaModalClickHandler);
    registerModalEventHandler('keydown', mediaModalKeydownHandler);

    // global, custom event
    document.addEventListener('liteYoutubeIframeLoaded', liteYoutubeHandler);

    // initial init
    initMediaModalEvents();
    document.querySelectorAll('[data-media-modal]').forEach(setupMediaModal);

    // reinit on update of previews
    document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
        players = {};
        const mediaManager = e.detail.mediaManager;
        console.log('reinitialize modals for media manager', mediaManager);

        reinitModalEvents();
        registerModalEventHandler('click', mediaModalClickHandler);
        registerModalEventHandler('keydown', mediaModalKeydownHandler);

        mediaManager.querySelectorAll('[data-media-modal]').forEach(setupMediaModal);
    });
});
