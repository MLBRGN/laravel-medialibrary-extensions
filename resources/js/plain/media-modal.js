// Functionality implemented with assistance from AI (ChatGPT)
// noinspection JSUnresolvedReference
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';
import { getCarouselController } from '@/js/plain/media-carousel';

document.addEventListener('DOMContentLoaded', () => {
    const players = {}; // key: youtubeId, value: YT.Player instance

    function openModal(modalId, slideTo = 0) {
        console.log('openModal', modalId, 'slideTo', slideTo);
        const modal = document.querySelector(modalId);
        if (!modal) return console.warn('Modal not found:', modalId);

        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        trapFocus(modal);

        const carousel = modal.querySelector('[data-carousel]');
        const controller = getCarouselController(carousel);
        if (controller) controller.goToSlide(parseInt(slideTo), true);

        fireEvent('modalOpened', modal);
    }

    function closeModal(modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        releaseFocus(modal);
        fireEvent('modalClosed', modal);
    }

    // Delegated click handler
    document.addEventListener('click', (e) => {
        // e.stopPropagation();

        const target = e.target;
        console.log('target', target);
        const trigger = e.target.closest('[data-modal-trigger]');
        if (trigger) {
            console.log('found trigger', trigger);
            e.preventDefault();
            openModal(trigger.getAttribute('data-modal-trigger'), trigger.getAttribute('data-slide-to') || 0);
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

    // ESC key closes active modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-modal].active').forEach(closeModal);
        }
    });

    // Modal initialization for YT players and keyboard control
    function setupModal(modal) {
        // console.log('setupModal', modal);
        const carousel = modal.querySelector('[data-carousel]');
        const autoPlay = modal.hasAttribute('data-video-autoplay');

        // console.log('carousel', carousel);
        // Setup YouTube player when iframe loaded
        modal.addEventListener('liteYoutubeIframeLoaded', (e) => {
            const videoSlide = e.target.closest('[data-youtube-video-id]');
            if (!videoSlide) return;

            const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
            const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');

            players[youTubeId] = new YT.Player(iframe, {
                events: { onReady: () => { /* optionally autoplay here */ } },
            });
        });

        // Play video on modal open
        modal.addEventListener('modalOpened', () => {
            if (!autoPlay) return;

            const activeSlide = modal.querySelector('.media-carousel-item.active');
            if (!activeSlide) return;

            const videoWrapper = activeSlide.querySelector('.media-video-container[data-youtube-video-id]');
            if (!videoWrapper) return;

            startVideoPlayBack(videoWrapper.getAttribute('data-youtube-video-id'));
        });

        // Stop videos on close
        modal.addEventListener('modalClosed', stopAllVideoPlayBack);

        // Keyboard control inside modal
        modal.addEventListener('keydown', (e) => {
            if (!modal.classList.contains('active')) return;

            const carousel = modal.querySelector('[data-carousel]');
            if (!carousel) return;

            const controller = getCarouselController(carousel);
            if (!controller) return;

            const isInsideCarousel = carousel.contains(e.target);

            if (e.key === 'Escape') {
                closeModal(modal);
                e.stopPropagation();
                e.preventDefault();
                return;
            }

            if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) {
                return; // Let carousel handle it
            }

            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                e.stopPropagation();
                controller.goToPreviousSlide();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                e.stopPropagation();
                controller.goToNextSlide();
            }
        });

        document.addEventListener('onImageUpdated', (e) => {
            console.log('✅ Global listener got event:', e.detail);
        });

        modal.addEventListener('onImageUpdated', (e) => {
            console.log('onImageUpdated', e);
        })

        // Handle carousel slide event
        if (carousel) {
            carousel.addEventListener('carouselSlided', () => {
                pauseAllVideoPlayBack();

                if (!autoPlay) return;

                const activeSlide = carousel.querySelector('.media-carousel-item.active');
                if (!activeSlide) return;

                const videoWrapper = activeSlide.querySelector('.media-video-container[data-youtube-video-id]');
                if (!videoWrapper) return;

                startVideoPlayBack(videoWrapper.getAttribute('data-youtube-video-id'));
            });
        }
    }

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

    // Initialize all modals on page load
    document.querySelectorAll('[data-modal]').forEach(setupModal);
});
