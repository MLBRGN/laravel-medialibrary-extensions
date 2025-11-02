// Functionality implemented with assistance from AI (ChatGPT)
import { fireEvent } from '@/js/plain/_helpers';
import { registerModalEventHandler, reinitModalEvents } from "@/js/plain/_modal-core";

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

    const slides = carousel.querySelectorAll('[data-carousel-item]');
    if (!slides.length) return;

    const indicators = carousel.querySelectorAll('[data-carousel-indicators] button');
    const prev = carousel.querySelector('[data-slide="prev"]');
    const next = carousel.querySelector('[data-slide="next"]');

    const ride = carousel.getAttribute('data-carousel-ride') === 'true';
    const rideInterval = Number(carousel.getAttribute('data-carousel-ride-interval') ?? '5000');
    const rideOnlyAfterInteraction = carousel.getAttribute('data-carousel-ride-only-after-interaction') === 'true';

    let currentSlideIndex = 0;
    let hasInteracted = false;
    let intervalId = null;
    let touchStartX = 0, touchEndX = 0, touchStartY = 0, touchEndY = 0;
    const swipeVerticalThreshold = 50;

    const updateCarousel = (toSlideIndex, direction = 'right', skipFireEvent = false) => {
        slides.forEach((slide) => {
            slide.classList.remove(
                'active',
                'slide-in-from-left',
                'slide-in-from-right',
                'slide-out-to-left',
                'slide-out-to-right'
            );
        });

        const currentSlide = slides[currentSlideIndex];
        const nextSlide = slides[toSlideIndex];

        if (carousel.getAttribute('data-carousel-effect') === 'slide') {
            const skipAnimation = carousel.classList.contains('temp-no-animation') || carousel.classList.contains('no-animation');

            if (!skipAnimation) {
                if (direction === 'right') {
                    nextSlide.classList.add('slide-in-from-right');
                    currentSlide.classList.add('slide-out-to-left');
                } else {
                    nextSlide.classList.add('slide-in-from-left');
                    currentSlide.classList.add('slide-out-to-right');
                }
                void nextSlide.offsetWidth; // force reflow
            }
        }

        currentSlide.classList.remove('active');
        nextSlide.classList.add('active');

        indicators.forEach((btn, i) => btn.classList.toggle('active', i === toSlideIndex));
        currentSlideIndex = toSlideIndex;

        fireEvent('mleCarouselSlided', carousel, {'carousel': carousel, 'currentSlide': nextSlide, 'currentSlideIndex': currentSlideIndex});
    };

    const goToSlide = (slideIndex, skipAnimation = false, skipFireEvent = false) => {
        if (slideIndex === currentSlideIndex) return;

        const normalizedIndex = (slideIndex + slides.length) % slides.length;
        const diff = normalizedIndex - currentSlideIndex;
        const direction = (diff + slides.length) % slides.length > slides.length / 2 ? 'left' : 'right';

        if (skipAnimation) {
            carousel.classList.add('temp-no-animation');
            void carousel.offsetWidth; // force reflow
        }

        updateCarousel(normalizedIndex, direction, skipFireEvent);

        if (skipAnimation) {
            carousel.classList.remove('temp-no-animation');
        }
    };

    const handleGesture = () => {
        const swipeThreshold = 40;
        const distanceX = touchEndX - touchStartX;

        if (Math.abs(distanceX) > swipeThreshold && Math.abs(distanceX) > Math.abs(touchEndY - touchStartY)) {
            if (distanceX < 0) goToSlide((currentSlideIndex + 1) % slides.length);
            else goToSlide((currentSlideIndex - 1 + slides.length) % slides.length);
            handleInteraction();
        }

        touchStartX = touchEndX = touchStartY = touchEndY = 0;
    };

    const startAutoRide = () => {
        stopAutoRide();
        intervalId = setInterval(() => goToSlide((currentSlideIndex + 1) % slides.length), rideInterval);
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

    prev?.addEventListener('click', () => { goToSlide((currentSlideIndex - 1 + slides.length) % slides.length); handleInteraction(); });
    next?.addEventListener('click', () => { goToSlide((currentSlideIndex + 1) % slides.length); handleInteraction(); });

    carousel.addEventListener('mouseenter', stopAutoRide);
    carousel.addEventListener('mouseleave', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });
    carousel.addEventListener('focusin', stopAutoRide);
    carousel.addEventListener('focusout', () => { if (ride && (!rideOnlyAfterInteraction || hasInteracted)) startAutoRide(); });

    carousel.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { e.preventDefault(); goToSlide((currentSlideIndex - 1 + slides.length) % slides.length); handleInteraction(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); goToSlide((currentSlideIndex + 1) % slides.length); handleInteraction(); }
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

    if (slides.length > 1 && ride && !rideOnlyAfterInteraction) startAutoRide();

    // controller API
    const controller = {
        goToSlide: (slideIndex, skipAnimation = false, skifFireEvent = false) => goToSlide(slideIndex, skipAnimation, skifFireEvent),
        getCurrentSlideIndex: () => currentSlideIndex,
        goToNextSlide: () => goToSlide((currentSlideIndex + 1) % slides.length),
        goToPreviousSlide: () => goToSlide((currentSlideIndex - 1 + slides.length) % slides.length),
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
