// Functionality implemented with assistance from AI (ChatGPT)
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-carousel]').forEach(carousel => {
        const items = carousel.querySelectorAll('.carousel-item');
        const indicators = carousel.querySelectorAll('.carousel-indicators button');
        const prev = carousel.querySelector('[data-slide="prev"]');
        const next = carousel.querySelector('[data-slide="next"]');

        const ride = carousel.getAttribute('data-carousel-ride') === 'true';
        const rideInterval = Number(carousel.getAttribute('data-carousel-ride-interval') ?? '5000');
        const rideOnlyAfterInteraction = carousel.getAttribute('data-carousel-ride-only-after-interaction') === 'true';

        let currentIndex = 0;
        let hasInteracted = false;
        let intervalId = null;
        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;
        const swipeVerticalThreshold = 50;

        // const updateCarousel = (index) => {
        //     items.forEach((item, i) => item.classList.toggle('active', i === index));
        //     indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));
        // };

        const updateCarousel = (index, direction = 'right') => {
            items.forEach((item, i) => {
                item.classList.remove('active', 'slide-left', 'slide-right');
                item.style.zIndex = 0;
            });

            const current = items[currentIndex];
            const next = items[index];

            if (carousel.getAttribute('data-carousel-effect') !== 'fade') {
                if (direction === 'right') {
                    current.classList.add('slide-left');
                    next.classList.add('slide-right');
                } else {
                    current.classList.add('slide-right');
                    next.classList.add('slide-left');
                }
            }

            // Force reflow to restart animation
            void next.offsetWidth;

            current.classList.remove('active');
            next.classList.add('active');

            indicators.forEach((btn, i) => btn.classList.toggle('active', i === index));

            currentIndex = index;

            const event = new CustomEvent('carouselSlided', {
                bubbles: true,    // Optional: allows the event to bubble up the DOM tree
                detail: { carousel }  // Optional: you can pass additional data here
            });
            carousel.dispatchEvent(event);
        };

        // const goToSlide = (index) => {
        //     currentIndex = (index + items.length) % items.length;
        //     updateCarousel(currentIndex);
        // };

        // const goToSlide = (index) => {
        //     const direction = index > currentIndex ? 'right' : 'left';
        //     if (index === currentIndex) return;
        //     updateCarousel((index + items.length) % items.length, direction);
        // };

        const goToSlide = (index) => {
            if (index === currentIndex) return;

            const normalizedIndex = (index + items.length) % items.length;
            const diff = normalizedIndex - currentIndex;
            const direction = (diff + items.length) % items.length > items.length / 2 ? 'left' : 'right';

            updateCarousel(normalizedIndex, direction);
        };

        const handleGesture = () => {
            const swipeThreshold = 40; // Minimum distance (in px) for a valid swipe
            const distance = touchEndX - touchStartX;

            if (Math.abs(distance) > swipeThreshold) {
                if (distance < 0) {
                    // Swiped left → next
                    goToSlide((currentIndex + 1) % items.length);
                } else {
                    // Swiped right → prev
                    goToSlide((currentIndex - 1 + items.length) % items.length);
                }
                handleInteraction();
            }
        };

        const startAutoRide = () => {
            stopAutoRide(); // always clear first to avoid duplicates
            intervalId = setInterval(() => {
                goToSlide((currentIndex + 1) % items.length);
            }, rideInterval);
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
                if (ride && rideOnlyAfterInteraction) {
                    startAutoRide();
                }
            }

            if (ride && !rideOnlyAfterInteraction) {
                startAutoRide();
            }
        };

        indicators.forEach((btn, i) => {
            btn.addEventListener('click', () => {
                goToSlide(i);
                handleInteraction();
            });
        });

        prev?.addEventListener('click', () => {
            goToSlide((currentIndex - 1 + items.length) % items.length);
            handleInteraction();
        });

        next?.addEventListener('click', () => {
            goToSlide((currentIndex + 1) % items.length);
            handleInteraction();
        });

        carousel.addEventListener('mouseenter', stopAutoRide);
        carousel.addEventListener('mouseleave', () => {
            if (ride && (!rideOnlyAfterInteraction || hasInteracted)) {
                startAutoRide();
            }
        });

        carousel.addEventListener('focusin', stopAutoRide);
        carousel.addEventListener('focusout', () => {
            if (ride && (!rideOnlyAfterInteraction || hasInteracted)) {
                startAutoRide();
            }
        });

        carousel.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                goToSlide((currentIndex - 1 + items.length) % items.length);
                handleInteraction();
            } else if (e.key === 'ArrowRight') {
                goToSlide((currentIndex + 1) % items.length);
                handleInteraction();
            }
        });

        carousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            touchStartY = e.changedTouches[0].screenY;
        }, { passive: true });

        carousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            touchEndY = e.changedTouches[0].screenY;

            if (Math.abs(touchEndY - touchStartY) < swipeVerticalThreshold) { // only react to mostly horizontal swipes
                handleGesture();
            }
        }, { passive: true });

        if (items.length > 1 && ride && !rideOnlyAfterInteraction) {
            startAutoRide();
        }
    });
});
