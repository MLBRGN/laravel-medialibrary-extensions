export let mle_bootstrap = null;

export function setMleBootstrapInstance(instance) {
    mle_bootstrap = instance;
}

export function getMleBootstrapInstance() {
    const bs = mle_bootstrap || window.bootstrap;

    if (!bs) {
        console.error("Bootstrap is not loaded. Host app must load bootstrap and expose window.bootstrap.");
        throw new Error("Bootstrap is not loaded. Host app must load bootstrap and expose window.bootstrap.");
    }

    return bs;
}
