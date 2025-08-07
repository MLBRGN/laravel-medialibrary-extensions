// modal-video-carousel.js
import { closeModal, openModal, setupModalBase } from './modal-core';
import { getCarouselController } from '@/js/plain/media-carousel';

const players = {}; // youtubeId: YT.Player

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

function setupVideoCarouselModal(modal) {
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

// Auto-init
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-modal]').forEach(setupVideoCarouselModal);

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-modal-trigger]');
        if (trigger) {
            e.preventDefault();
            const modalId = trigger.getAttribute('data-modal-trigger');
            const slideTo = trigger.getAttribute('data-slide-to') || 0;
            const modal = document.querySelector(modalId);
            const controller = getCarouselController(modal?.querySelector('[data-carousel]'));
            openModal(modalId, slideTo, controller);
            return;
        }

        const closeBtn = e.target.closest('[data-modal-close]');
        if (closeBtn) {
            const modal = closeBtn.closest('[data-modal]');
            if (modal) closeModal(modal);
            return;
        }

        const modal = e.target.closest('[data-modal]');
        if (modal && e.target === modal) closeModal(modal);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal].active').forEach(closeModal);
        }
    });
});
