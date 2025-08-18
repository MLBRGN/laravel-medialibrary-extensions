// Functionality implemented with assistance from AI (ChatGPT)
import { fireEvent } from '@/js/plain/helpers';
import { registerModalEventHandler, reinitModalEvents } from "@/js/plain/modal-core";

const carousels = new Map();

export function getCarouselController(element) {
    return carousels.get(element);
}

export function initCarousel(carousel) {
    // Remove existing controller if it exists (for reinit)
    if (carousels.has(carousel)) {
        carousels.get(carousel).pause();
        carousels.delete(carousel);
    }

    const items = carousel.querySelectorAll('.media-carousel-item');
    if (!items.length) return;

    const indicators = carousel.querySelectorAll('.media-carousel-indicators button');
    const prev = carousel.querySelector('[data-slide="prev"]');
    const next = carousel.querySelector('[data-slide="next"]');

    const ride = carousel.getAttribute('data-carousel-ride') === 'true';
    const rideInterval = Number(carousel.getAttribute('data-carousel-ride-interval') ?? '5000');
    const rideOnlyAfterInteraction = carousel.getAttribute('data-carousel-ride-only-after-interaction') === 'true';

    let currentIndex = 0;
    let hasInteracted = false;
    let intervalId = null;
    let touchStartX = 0, touchEndX = 0, touchStartY = 0, touchEndY = 0;
    const swipeVerticalThreshold = 50;

    const updateCarousel = (index, direction = 'right') => {
        items.forEach((item) => {
            item.classList.remove(
                'active',
                'slide-in-from-left',
                'slide-in-from-right',
                'slide-out-to-left',
                'slide-out-to-right'
            );
        });

        const current = items[currentIndex];
        const nextItem = items[index];

        if (carousel.getAttribute('data-carousel-effect') === 'slide') {
            const skipAnimation = carousel.classList.contains('temp-no-animation') || carousel.classList.contains('no-animation');

            if (!skipAnimation) {
                if (direction === 'right') {
                    nextItem.classList.add('slide-in-from-right');
                    current.classList.add('slide-out-to-left');
                } else {
                    nextItem.classList.add('slide-in-from-left');
                    current.classList.add('slide-out-to-right');
                }
                void nextItem.offsetWidth; // force reflow
            }
        }

        current.classList.remove('active');
        nextItem.classList.add('active');

        indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
        currentIndex = index;

        fireEvent('carouselSlided', carousel);
    };

    const goToSlide = (index, skipAnimation = false) => {
        if (index === currentIndex) return;

        const normalizedIndex = (index + items.length) % items.length;
        const diff = normalizedIndex - currentIndex;
        const direction = (diff + items.length) % items.length > items.length / 2 ? 'left' : 'right';

        if (skipAnimation) {
            carousel.classList.add('temp-no-animation');
            void carousel.offsetWidth; // force reflow
        }

        updateCarousel(normalizedIndex, direction);

        if (skipAnimation) {
            carousel.classList.remove('temp-no-animation');
        }
    };

    const handleGesture = () => {
        const swipeThreshold = 40;
        const distanceX = touchEndX - touchStartX;

        if (Math.abs(distanceX) > swipeThreshold && Math.abs(distanceX) > Math.abs(touchEndY - touchStartY)) {
            if (distanceX < 0) goToSlide((currentIndex + 1) % items.length);
            else goToSlide((currentIndex - 1 + items.length) % items.length);
            handleInteraction();
        }

        touchStartX = touchEndX = touchStartY = touchEndY = 0;
    };

    const startAutoRide = () => {
        stopAutoRide();
        intervalId = setInterval(() => goToSlide((currentIndex + 1) % items.length), rideInterval);
    };

    const stopAutoRide = () => {
        if (intervalId !== null) {
            clearInterval(intervalId);
            intervalId = null;
        }
    };

    const handleInteraction = () => {
        if (!hasInteracted) {
            hasInteracted = true;
            if (ride && rideOnlyAfterInteraction) startAutoRide();
        }
        if (ride && !rideOnlyAfterInteraction) startAutoRide();
    };

    indicators.forEach((btn, i) => btn.addEventListener('click', () => {
        goToSlide(i);
        handleInteraction();
    }));

    prev?.addEventListener('click', () => { goToSlide((currentIndex - 1 + items.length) % items.length); handleInteraction(); });
    next?.addEventListener('click', () => { goToSlide((currentIndex + 1) % items.length); handleInteraction(); });

    carousel.addEventListener('mouseenter', stopAutoRide);
    carousel.addEventListener('mouseleave', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });
    carousel.addEventListener('focusin', stopAutoRide);
    carousel.addEventListener('focusout', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });

    carousel.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { e.preventDefault(); goToSlide((currentIndex - 1 + items.length) % items.length); handleInteraction(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); goToSlide((currentIndex + 1) % items.length); handleInteraction(); }
    });

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;

        if (Math.abs(touchEndY - touchStartY) < swipeVerticalThreshold) handleGesture();
    }, { passive: true });

    if (items.length > 1 && ride && !rideOnlyAfterInteraction) startAutoRide();

    // controller API
    const controller = {
        goToSlide: (index, skipAnimation = false) => goToSlide(index, skipAnimation),
        getCurrentIndex: () => currentIndex,
        goToNextSlide: () => goToSlide((currentIndex + 1) % items.length),
        goToPreviousSlide: () => goToSlide((currentIndex - 1 + items.length) % items.length),
        pause: stopAutoRide,
        resume: startAutoRide,
    };

    carousels.set(carousel, controller);
}

// Reinit on updates
document.addEventListener('mediaManagerPreviewsUpdated', (e) => {
    const mediaManager = e.detail.mediaManager;
    document.querySelectorAll('[data-carousel]').forEach(initCarousel);
});

document.querySelectorAll('[data-carousel]').forEach(initCarousel);
