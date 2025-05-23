// Open modal
document.querySelectorAll('[data-toggle="modal"]').forEach(trigger => {
    trigger.addEventListener('click', () => {
        const targetId = trigger.getAttribute('data-target');
        const modal = document.querySelector(targetId);
        if (modal) {
            modal.classList.add('show');
        }
    });
});

// Close modal on [data-dismiss="modal"] or outside click
document.querySelectorAll('.modal').forEach(modal => {
    // Close on close button
    modal.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', () => modal.classList.remove('show'));
    });

    // Close on backdrop click
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });
});

// Close modal on ESC key
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal.show').forEach(modal => modal.classList.remove('show'));
    }
});
