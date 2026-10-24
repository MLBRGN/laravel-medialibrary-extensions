// noinspection ES6UnusedImports

import '@/js/bootstrap-6/bootstrap-resolver'
import '@/js/shared/image-fallback';
import '@/css/bootstrap-6.scss';

// Minimal Bootstrap 6 Dialog polyfill for data-bs-toggle="dialog"
// This ensures our components work even if the full Bootstrap 6 JS isn't loaded or available (e.g. in test environment)
document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-bs-toggle="dialog"]');
    if (toggle) {
        event.preventDefault();
        const targetSelector = toggle.getAttribute('data-bs-target');
        const target = document.querySelector(targetSelector);
        if (target && target.tagName === 'DIALOG') {
            if (typeof target.showModal === 'function') {
                // Dispatch show.bs.dialog event
                const showEvent = new CustomEvent('show.bs.dialog', {
                    detail: { relatedTarget: toggle },
                    bubbles: true,
                    cancelable: true
                });
                // Add relatedTarget directly to the event object for easier access in listeners
                showEvent.relatedTarget = toggle;

                target.dispatchEvent(showEvent);

                if (!showEvent.defaultPrevented) {
                    target.showModal();
                    
                    const shownEvent = new CustomEvent('shown.bs.dialog', {
                        detail: { relatedTarget: toggle },
                        bubbles: true
                    });
                    shownEvent.relatedTarget = toggle;
                    target.dispatchEvent(shownEvent);
                }
            }
        }
    }

    const dismiss = event.target.closest('[data-bs-dismiss="dialog"]');
    if (dismiss) {
        const dialog = dismiss.closest('dialog');
        if (dialog) {
            const hideEvent = new CustomEvent('hide.bs.dialog', {
                bubbles: true,
                cancelable: true
            });
            dialog.dispatchEvent(hideEvent);

            if (!hideEvent.defaultPrevented) {
                dialog.close();
                dialog.dispatchEvent(new CustomEvent('hidden.bs.dialog', {
                    bubbles: true
                }));
            }
        }
    }
});
