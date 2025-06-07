document.addEventListener('DOMContentLoaded', () => {
    const openModal = (modalId) => {
        console.log('openModal', modalId);
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.log('modal not found', modalId);
            return;
        }
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = (modal) => {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };

    // Attach to all close buttons
    document.querySelectorAll('[data-modal-close]').forEach(element => {
        element.addEventListener('click', (e) => {
            const modal = element.closest('.media-modal');
            if (modal) closeModal(modal);
        });
    });

    // Optional: clicking outside modal-content also closes
    document.querySelectorAll('.media-modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
        });
    });

    // Optional: add triggers
    document.querySelectorAll('[data-modal-trigger]').forEach(element => {
        element.addEventListener('click', () => {
            const target = element.getAttribute('data-modal-trigger');
            openModal(target);
        });
    });
});
//
// document.addEventListener('DOMContentLoaded', () => {
//     console.log( document.querySelectorAll('[data-toggle="modal"]'))
//     // Open modal
//     document.querySelectorAll('[data-toggle="modal"]').forEach(trigger => {
//         console.log('plain register trigger', trigger);
//         trigger.addEventListener('click', () => {
//             const targetId = trigger.getAttribute('data-target');
//             const modal = document.querySelector(targetId);
//             if (modal) {
//                 modal.classList.add('show');
//             }
//         });
//     });
//
//     // Close modal on [data-dismiss="modal"] or outside click
//     document.querySelectorAll('.modal').forEach(modal => {
//         // Close on close button
//         modal.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
//             btn.addEventListener('click', () => modal.classList.remove('show'));
//         });
//
//         // Close on backdrop click
//         modal.addEventListener('click', (event) => {
//             if (event.target === modal) {
//                 modal.classList.remove('show');
//             }
//         });
//     });
//
//     // Close modal on ESC key
//     document.addEventListener('keydown', (event) => {
//         if (event.key === 'Escape') {
//             document.querySelectorAll('.modal.show').forEach(modal => modal.classList.remove('show'));
//         }
//     });
// });
