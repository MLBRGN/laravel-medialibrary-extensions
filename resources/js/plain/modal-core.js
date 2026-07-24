// modal-core.js
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';

let listenersBound = false;
const modalInitializers = new Map();
let observerStarted = false;

export function registerModalInitializer(selector, initializer) {
    modalInitializers.set(selector, initializer);

    document.querySelectorAll(selector)
        .forEach(initializer);
}

// Event Handler Registry
const eventHandlers = new Map();
// structure: { "click": Set<fn>, "keydown": Set<fn>, ... }

export function registerModalEventHandler(type, fn) {
    if (!eventHandlers.has(type)) {
        eventHandlers.set(type, new Set());
    }
    eventHandlers.get(type).add(fn);
}

export function unregisterModalEventHandler(type, fn) {
    if (eventHandlers.has(type)) {
        eventHandlers.get(type).delete(fn);
    }
}

// Internal event dispatcher
function eventDispatcher(type, e) {
    if (!eventHandlers.has(type)) return;
    for (const handler of eventHandlers.get(type)) {
        handler(e);
    }
}

export function closeModal(modal, originalEvent) {
    releaseFocus(modal);

    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    // document.body.style.overflow = 'initial';
    document.body.style.overflow = '';

    fireEvent('mleModalClosed', modal, {
        modal: modal,  originalEvent: originalEvent
    });
}

export function openModal(modalId, trigger, originalEvent) {
    const modal = document.querySelector(modalId);
    if (!modal) {
        console.warn('could not find modal ' + modalId);
        return;
    }

    modal.classList.add('active');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    trapFocus(modal);
    fireEvent('mleModalOpened', modal, {
        modal: modal,
        trigger: trigger,
        originalEvent: originalEvent
    });
}

export function setupModalLifecycle(modal, onClose = () => {}, onOpen = () => {}) {
    modal.addEventListener('mleModalOpened', onOpen);
    modal.addEventListener('mleModalClosed', onClose);
}

function defaultClickHandler(e) {
    e.stopPropagation();

    const target = e.target;
    const trigger = target.closest('[data-mle-modal-trigger]');
    if (trigger) {
        e.preventDefault();
        const modalId = trigger.getAttribute('data-mle-modal-trigger');
        openModal(modalId, trigger, e);
        return;
    }

    const closeBtn = target.closest('[data-mle-modal-close]');
    if (closeBtn) {
        const modal = closeBtn.closest('[data-mle-modal]');
        if (modal) closeModal(modal, e);
        return;
    }

    const modal = target.closest('[data-mle-modal]');
    if (modal && target === modal) closeModal(modal, e);
}

function defaultKeydownHandler(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-mle-modal].active').forEach(closeModal);
    }
}

function initModalEvents() {
    if (!listenersBound) {
        document.addEventListener('click', e => eventDispatcher('click', e));
        document.addEventListener('keydown', e => eventDispatcher('keydown', e));
        listenersBound = true;
    }

    // ensure defaults are always registered
    registerModalEventHandler('click', defaultClickHandler);
    registerModalEventHandler('keydown', defaultKeydownHandler);
}

function initModalObserver() {
    if (observerStarted) {
        return;
    }

    observerStarted = true;

    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {

                if (!(node instanceof Element || node instanceof DocumentFragment)) {
                    continue;
                }

                for (const [selector, initializer] of modalInitializers) {

                    if (node instanceof Element && node.matches(selector)) {
                        initializer(node);
                    }

                    node.querySelectorAll?.(selector).forEach(initializer);
                }
            }
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

initModalEvents();
initModalObserver();


