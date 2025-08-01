// Functionality implemented with assistance from AI (ChatGPT)
// noinspection JSUnresolvedReference
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';
import { getCarouselController } from '@/js/plain/media-carousel';

document.addEventListener('DOMContentLoaded', () => {
    const players = {}; // key: youtubeId, value: YT.Player instance

    function openModal(modalId, slideTo = 0) {
        const modal = document.getElementById(modalId);
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
        const trigger = e.target.closest('[data-modal-trigger]');
        if (trigger) {
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
        const carousel = modal.querySelector('[data-carousel]');
        const autoPlay = modal.hasAttribute('data-video-autoplay');

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


// import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';
// import { getCarouselController } from '@/js/plain/media-carousel';
//
// document.addEventListener('DOMContentLoaded', () => {
//
//     const modals = document.querySelectorAll('[data-modal]');
//     const players = {}; // Store player instances by slide ID
//
//     const openModal = (modalId, slideTo) => {
//         const modal = document.getElementById(modalId);
//         if (!modal) {
//             console.log('modal not found', modalId);
//             return;
//         }
//         modal.classList.add('active');
//         modal.setAttribute('aria-hidden', 'false');
//         document.body.style.overflow = 'hidden';
//
//         trapFocus(modal);
//
//         const carousel = modal.querySelector('[data-carousel]');
//         const controller = getCarouselController(carousel);
//
//         if (controller && slideTo !== undefined && slideTo !== null) {
//             controller.goToSlide(parseInt(slideTo), true);
//         }
//
//         fireEvent('modalOpened', modal);
//
//     };
//
//     const closeModal = (modal) => {
//         modal.classList.remove('active');
//         modal.setAttribute('aria-hidden', 'true');
//         document.body.style.overflow = '';
//
//         releaseFocus(modal);
//         fireEvent('modalClosed', modal);
//     };
//
//     // Event delegation
//     document.addEventListener('click', (e) => {
//         const trigger = e.target.closest('[data-modal-trigger]');
//         if (trigger) {
//             e.preventDefault();
//             const target = trigger.getAttribute('data-modal-trigger');
//             const slideTo = trigger.getAttribute('data-slide-to') ?? 0;
//             openModal(target, slideTo);
//             return;
//         }
//
//         const closeBtn = e.target.closest('[data-modal-close]');
//         if (closeBtn) {
//             const modal = closeBtn.closest('.media-modal');
//             if (modal) closeModal(modal);
//             return;
//         }
//
//         const modal = e.target.closest('[data-modal]');
//         if (modal && e.target === modal) {
//             closeModal(modal);
//         }
//     });
//
//     // ESC key to close any active modal
//     document.addEventListener('keydown', (e) => {
//         if (e.key === 'Escape') {
//             document.querySelectorAll('.media-modal.active').forEach(modal => {
//                 closeModal(modal);
//             });
//         }
//     });
//
//     modals.forEach((modal) => {
//         const carousel = modal.querySelector('[data-carousel]');
//         let autoPlay = modal.hasAttribute('data-video-autoplay');
//
//         function setupYT(videoSlide) {
//             const youTubeId = videoSlide.getAttribute('data-youtube-video-id');
//             const iframe = videoSlide.querySelector('lite-youtube').shadowRoot.querySelector('iframe');
//             // instantiate the new player instance for slide
//             players[youTubeId] = new YT.Player(iframe, {
//                 events: {
//                     onReady: () => {
//                         // console.log('YouTube ready');
//                         // console.log('videoSlide', videoSlide);
//                         // if (autoPlay) {
//                         //     startVideoPlayBack(youTubeId);
//                         // }
//                     },
//                 },
//             });
//         }
//
//         // Event listener for when the YouTube iframe loads
//         modal.addEventListener('liteYoutubeIframeLoaded', (event) => {
//             const targetSlide = event.target.closest('[data-youtube-video-id]');
//             if (targetSlide) setupYT(targetSlide);
//         });
//
//         modal.addEventListener('modalOpened', (e) => {
//             if (!modal.hasAttribute('data-video-autoplay')) return;
//
//             const activeSlide = modal.querySelector('.media-carousel-item.active');
//             if (!activeSlide) return;
//
//             const videoWrapper = activeSlide.querySelector('.media-video-container');
//             if (!videoWrapper || !videoWrapper.hasAttribute('data-youtube-video-id')) return;
//
//             const youtubeId = videoWrapper.getAttribute('data-youtube-video-id');
//             startVideoPlayBack(youtubeId);
//         });
//
//         // Stop the video when the modal is closed
//         modal.addEventListener('modalClosed', () => {
//             stopAllVideoPlayBack();
//         });
//
//         modal.addEventListener('keydown', (e) => {
//             if (!modal.classList.contains('active')) return;
//
//             const carousel = modal.querySelector('[data-carousel]');
//             if (!carousel) return;
//
//             const controller = getCarouselController(carousel);
//             if (!controller) return;
//
//             const isInsideCarousel = carousel.contains(e.target);
//
//             // Escape should always work
//             if (e.key === 'Escape') {
//                 document.querySelectorAll('.media-modal.active').forEach(activeModal => {
//                     closeModal(activeModal);
//                 });
//                 e.stopPropagation();
//                 e.preventDefault();
//             }
//
//             if ((e.key === 'ArrowLeft' || e.key === 'ArrowRight') && isInsideCarousel) {
//                 return; // let the carousel's listener handle it
//             }
//
//             // If outside the carousel (e.g., focus on close button), we handle the keys here
//             if (e.key === 'ArrowLeft') {
//                 e.preventDefault();
//                 e.stopPropagation();
//                 controller.goToPreviousSlide();
//             } else if (e.key === 'ArrowRight') {
//                 e.preventDefault();
//                 e.stopPropagation();
//                 controller.goToNextSlide();
//             }
//         });
//
//         // Handle carousel sliding
//         carousel.addEventListener('carouselSlided', (event) => {
//             pauseAllVideoPlayBack();
//
//             if (modal.hasAttribute('data-video-autoplay')) {
//                 // Only get the active carousel item
//                 const activeSlide = carousel.querySelector('.media-carousel-item.active');
//
//                 if (!activeSlide) return;
//
//                 const videoWrapper = activeSlide.querySelector('.media-video-container');
//
//                 if (videoWrapper && videoWrapper.hasAttribute('data-youtube-video-id')) {
//                     const youtubeId = videoWrapper.getAttribute('data-youtube-video-id');
//                     startVideoPlayBack(youtubeId);
//                 }
//             }
//         });
//     });
//
//     function stopAllVideoPlayBack() {
//         // TODO first check if playing
//         // console.log('stopAllVideoPlayBack');
//         Object.values(players).forEach((player) => {
//             player.stopVideo()
//         });
//     }
//
//     function pauseAllVideoPlayBack() {
//         // TODO first check if playing
//         // console.log('pauseAllVideoPlayBack');
//         Object.values(players).forEach((player) => {
//             player.pauseVideo()
//         });
//     }
//
//     function startVideoPlayBack(youTubeId) {
//         // console.log('startVideoPlayBack');
//         let player = players[youTubeId]
//         // console.log('player', player);
//         if (player) {
//             player.playVideo();
//         }
//     }
//
// });
