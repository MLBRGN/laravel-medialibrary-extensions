// modal-core.js
import { fireEvent, trapFocus, releaseFocus } from '@/js/plain/helpers';

let listenersBound = false;

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
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = 'initial';

    releaseFocus(modal);
    fireEvent('mleModalClosed', modal, {
        modal: modal,  originalEvent: originalEvent
    });
}

export function openModal(modalId, trigger, originalEvent) {
    const modal = document.querySelector(modalId);
    if (!modal) {
        // console.log('could not find modal ' + modalId);
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

export function setupModalBase(modal, onClose = () => {}, onOpen = () => {}) {
    modal.addEventListener('mleModalOpened', onOpen);
    modal.addEventListener('mleModalClosed', onClose);
}

function defaultClickHandler(e) {
    e.stopPropagation();

    const target = e.target;
    const trigger = target.closest('[data-modal-trigger]');
    if (trigger) {
        e.preventDefault();
        const modalId = trigger.getAttribute('data-modal-trigger');
        openModal(modalId, trigger, e);
        return;
    }

    const closeBtn = target.closest('[data-modal-close]');
    if (closeBtn) {
        const modal = closeBtn.closest('[data-modal]');
        if (modal) closeModal(modal, e);
        return;
    }

    const modal = target.closest('[data-modal]');
    if (modal && target === modal) closeModal(modal, e);
}

function defaultKeydownHandler(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[data-modal].active').forEach(closeModal);
    }
}

export function initModalEvents() {
    if (!listenersBound) {
        document.addEventListener('click', e => eventDispatcher('click', e));
        document.addEventListener('keydown', e => eventDispatcher('keydown', e));
        listenersBound = true;
    }

    // ensure defaults are always registered
    registerModalEventHandler('click', defaultClickHandler);
    registerModalEventHandler('keydown', defaultKeydownHandler);
}

export function reinitModalEvents() {
    eventHandlers.clear();// clear only registered handlers
    initModalEvents();// but don’t re-bind the DOM listeners
}


