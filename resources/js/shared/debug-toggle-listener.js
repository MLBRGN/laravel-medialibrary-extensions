document.addEventListener('click', function (e) {
    // console.log('click', e);
    const target = e.target.closest('[data-mle-action="debugger-toggle"]');
    // console.log('debug toggle', target)
    if (!target) return;

    e.preventDefault();

    // Find the closest MLE component container
    let component = target.closest('.mle-component');
    // console.log('closest component', component)
    if (!component) return;

    // If the component found is the debug menu, we need to go up further
    // to find the actual main component container that holds the debug panel
    if (component.classList.contains('mle-debug-menu')) {
        component = component.closest('.mle-component:not(.mle-debug-menu)');
        // console.log('actual component', component)
    }

    if (!component) return;

    // Toggle the debug panel within this component
    const debugPanel = component.querySelector('[data-mle-debug]');
    // console.log('closest debug panel', debugPanel)
    if (debugPanel) {
        // console.log('toggle debug panel')
        debugPanel.classList.toggle('hidden');
        debugPanel.classList.toggle('mle-hidden');
    }
});
