import { setupModalBase, closeModal } from './modal-core';

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-image-editor-modal]').forEach(modal => {
        setupModalBase(modal, () => {}, () => {});
    });
});
