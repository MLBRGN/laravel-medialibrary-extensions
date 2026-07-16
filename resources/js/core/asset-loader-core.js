// NOTE: This file is duplicated across mlbrgn packages.
// If you update it, sync changes across packages.

const globalLoadedScripts = new Set();
const globalLoadedStyles = new Set();
const globallyEnsured = new Set(); // absolute URLs already ensured (scripts/styles)

/**
 * Create a package-scoped loader
 */
export function createAssetLoader(namespace, {
        globalDedup = false,
        basePath
    } = {}) {
    // console.log('mle createAssetLoader, basePath: ', basePath)

    if (!basePath) {
        throw new Error(
            `[mlbrgn] Missing basePath for asset loader (${namespace})`
        );
    }

    const loadedScripts = new Set();
    const loadedStyles = new Set();

    function shouldSkip(set, globalSet, key, globalKey) {
        if (set.has(key)) return true;
        if (globalDedup && globalSet.has(globalKey)) return true;
        return false;
    }

    function markLoaded(set, globalSet, key, globalKey) {
        // console.log(key, 'loaded')

        set.add(key);
        if (globalDedup) globalSet.add(globalKey);
    }

    function resolveUrl(path) {
        if (/^(https?:)?\/\//.test(path)) {
            return path;
        }

        return `${basePath}/${path}`;
    }

    /**
     * Ensure a script exists (idempotent, de-duped globally and per-loader)
     */
    function ensureScript(src, { type = 'module', async = false } = {}) {
        const fullSrc = resolveUrl(src);
        const key = `${namespace}:${fullSrc}`;
        const globalKey = fullSrc;

        if (globallyEnsured.has(globalKey) || document.querySelector(`script[src="${CSS.escape(fullSrc)}"]`)) {
            // track in loader scope too for consistency
            loadedScripts.add(key);
            if (globalDedup) globalLoadedScripts.add(globalKey);
            return Promise.resolve();
        }

        // if loadScript already scheduled this asset, skip
        if (loadedScripts.has(key) || (globalDedup && globalLoadedScripts.has(globalKey))) {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = fullSrc;
            script.type = type;
            script.async = async;
            script.onload = () => {
                globallyEnsured.add(globalKey);
                loadedScripts.add(key);
                if (globalDedup) globalLoadedScripts.add(globalKey);
                resolve();
            };
            script.onerror = (e) => {
                console && console.warn && console.warn('[mlbrgn] Failed to load script', fullSrc, e);
                reject(e);
            };
            document.head.appendChild(script);
        });
    }

    /**
     * Ensure a stylesheet exists (idempotent, de-duped globally and per-loader)
     */
    function ensureStyle(href) {
        const fullHref = resolveUrl(href);
        const key = `${namespace}:${fullHref}`;
        const globalKey = fullHref;

        if (globallyEnsured.has(globalKey) || document.querySelector(`link[rel="stylesheet"][href="${CSS.escape(fullHref)}"]`)) {
            loadedStyles.add(key);
            if (globalDedup) globalLoadedStyles.add(globalKey);
            return Promise.resolve();
        }

        if (loadedStyles.has(key) || (globalDedup && globalLoadedStyles.has(globalKey))) {
            return Promise.resolve();
        }

        return new Promise((resolve, reject) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = fullHref;
            link.onload = () => {
                globallyEnsured.add(globalKey);
                loadedStyles.add(key);
                if (globalDedup) globalLoadedStyles.add(globalKey);
                resolve();
            };
            link.onerror = (e) => {
                console && console.warn && console.warn('[mlbrgn] Failed to load style', fullHref, e);
                reject(e);
            };
            document.head.appendChild(link);
        });
    }

    function loadScript(src, { type = 'module', async = false } = {}) {
        const fullSrc = resolveUrl(src);
        // console.log('mle: loadScript ', fullSrc)

        const key = `${namespace}:${fullSrc}`;
        const globalKey = fullSrc;

        if (shouldSkip(loadedScripts, globalLoadedScripts, key, globalKey)) {
            return Promise.resolve();
        }

        // mark immediately to prevent race
        loadedScripts.add(key);
        if (globalDedup) globalLoadedScripts.add(globalKey);

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = fullSrc;
            script.type = type;
            script.async = async;

            script.onload = resolve;
            script.onerror = reject;

            document.head.appendChild(script);
        });
    }

    function loadStyle(href) {
        const fullHref = resolveUrl(href);

        const key = `${namespace}:${fullHref}`;
        const globalKey = fullHref;

        if (shouldSkip(loadedStyles, globalLoadedStyles, key, globalKey)) {
            return;
        }

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = fullHref;

        document.head.appendChild(link);

        markLoaded(loadedStyles, globalLoadedStyles, key, globalKey);
    }

    return {
        loadScript,
        loadStyle,
        ensureScript,
        ensureStyle,
        resolveUrl
    };
}

/**
 * Global, basePath-agnostic helpers for lazy, on-demand loading by absolute URL.
 * These are safe to call multiple times and will not duplicate tags.
 */
export function ensureScript(src, { type = 'module', async = true } = {}) {
    const url = new URL(src, document.baseURI).toString();
    if (globallyEnsured.has(url) || document.querySelector(`script[src="${CSS.escape(url)}"]`)) {
        globallyEnsured.add(url);
        return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = url; s.type = type; s.async = async;
        s.onload = () => { globallyEnsured.add(url); resolve(); };
        s.onerror = (e) => { console && console.warn && console.warn('[mlbrgn] Failed to load script', url, e); reject(e); };
        document.head.appendChild(s);
    });
}

export function ensureStyle(href) {
    const url = new URL(href, document.baseURI).toString();
    if (globallyEnsured.has(url) || document.querySelector(`link[rel="stylesheet"][href="${CSS.escape(url)}"]`)) {
        globallyEnsured.add(url);
        return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
        const l = document.createElement('link');
        l.rel = 'stylesheet'; l.href = url;
        l.onload = () => { globallyEnsured.add(url); resolve(); };
        l.onerror = (e) => { console && console.warn && console.warn('[mlbrgn] Failed to load stylesheet', url, e); reject(e); };
        document.head.appendChild(l);
    });
}

/**
 * Collect JSON configs from DOM
 */
export function collectConfigs(selector) {
    return Array.from(document.querySelectorAll(selector))
        .map(el => {
            try {
                return JSON.parse(el.textContent.trim());
            } catch {
                console.warn('[mlbrgn] Invalid config JSON', el);
                return null;
            }
        })
        .filter(Boolean);
}

/**
 * Merge configs generically
 */
export function mergeConfigs(configs) {
    if (!configs.length) return null;

    const merged = {
        theme: configs[0].theme ?? null,
        assetBasePath: configs[0].assetBasePath ?? null,
        imageEditorTranslationsPath: configs[0].imageEditorTranslationsPath ?? null,
        assets: {},
        translations: {},
        debug: configs.some(c => c.debug),
    };

    for (const config of configs) {
        // Merge assets (boolean flags)
        for (const [key, value] of Object.entries(config.assets || {})) {
            if (value === true) {
                merged.assets[key] = true;
            }
        }

        // Merge translations
        Object.assign(merged.translations, config.translations || {});
    }

    // Warn on theme mismatch
    const themes = new Set(configs.map(c => c.theme).filter(Boolean));
    if (themes.size > 1) {
        console.warn('[mlbrgn] Multiple themes detected:', [...themes]);
    }

    return merged;
}
