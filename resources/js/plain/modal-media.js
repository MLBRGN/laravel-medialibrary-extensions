import {
    closeModal,
    initModalEvents as initBaseModalEvents,
    openModal as baseOpenModal,
    setupModalBase
} from './modal-core';

import { getCarouselController } from '@/js/plain/media-carousel';

const players = {}; // key: youtubeId, value: YT.Player instance

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

    modal.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('active')) return;

        const controller = getCarouselController(modal.querySelector('[data-carousel]'));
        if (!controller) return;

        const isInsideCarousel = modal.querySelector('[data-carousel]').contains(e.target);

        if (e.key === 'Escape') {
            closeModal(modal);
            e.preventDefault();
            return;
        }

        if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) return;

        if (e.key === 'ArrowLeft') {
            controller.goToPreviousSlide();
        } else if (e.key === 'ArrowRight') {
            controller.goToNextSlide();
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

    modal.addEventListener('liteYoutubeIframeLoaded', (e) => {
        const videoSlide = e.target.closest('[data-youtube-video-id]');
        const ytId = videoSlide?.getAttribute('data-youtube-video-id');
        const iframe = videoSlide?.querySelector('lite-youtube')?.shadowRoot?.querySelector('iframe');

        if (ytId && iframe) {
            players[ytId] = new YT.Player(iframe);
        }
    });
}

/**
 * Overrides modal trigger clicks to support carousel slideTo for media modals.
 */
function initMediaModalEvents() {
    initBaseModalEvents(); // still listen for Escape and close buttons

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-modal-trigger]');
        if (trigger) {
            const modalId = trigger.getAttribute('data-modal-trigger');
            const slideTo = parseInt(trigger.getAttribute('data-slide-to') || '0', 10);

            const modal = document.querySelector(modalId);
            baseOpenModal(modalId); // open modal first

            const carousel = modal?.querySelector('[data-carousel]');
            const controller = getCarouselController(carousel);
            if (controller) {
                controller.goToSlide(slideTo, true);
            }

            return;
        }
    });
}

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    initMediaModalEvents();

    document.querySelectorAll('[data-media-modal]').forEach(setupMediaModal);
});
