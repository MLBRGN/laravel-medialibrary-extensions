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

    function stopAllVideoPlayBack() {
        Object.values(players).forEach(p => p.stopVideo());
    }
    function pauseAllVideoPlayBack() {
        Object.values(players).forEach(p => p.pauseVideo());
    }
    function startVideoPlayBack(ytId) {
        const player = players[ytId];
        if (player) player.playVideo();
    }

    function setupMediaModal(modal) {
        const carousel = modal.querySelector('[data-carousel]');
        const autoPlay = modal.hasAttribute('data-video-autoplay');

        setupModalBase(modal, stopAllVideoPlayBack, () => {
            if (!autoPlay) return;

            const activeSlide = modal.querySelector('.media-carousel-item.active');
            const videoWrapper = activeSlide?.querySelector('.media-video-container[data-youtube-video-id]');
            if (videoWrapper) {
                startVideoPlayBack(videoWrapper.getAttribute('data-youtube-video-id'));
            }
        });

        carousel?.addEventListener('carouselSlided', () => {
            pauseAllVideoPlayBack();

            if (!autoPlay) return;

            const activeSlide = carousel.querySelector('.media-carousel-item.active');
            const videoWrapper = activeSlide?.querySelector('.media-video-container[data-youtube-video-id]');
            if (videoWrapper) {
                startVideoPlayBack(videoWrapper.getAttribute('data-youtube-video-id'));
            }
        });
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
        // TODO value correct, but not sliding
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

        const videoSlide = e.target.closest('[data-youtube-video-id]');
        const ytId = videoSlide?.getAttribute('data-youtube-video-id');
        const iframe = videoSlide?.querySelector('lite-youtube')?.shadowRoot?.querySelector('iframe');

        if (ytId && iframe && !players[ytId]) {
            players[ytId] = new YT.Player(iframe);
        }
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
