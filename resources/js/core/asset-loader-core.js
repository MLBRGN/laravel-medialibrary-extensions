// NOTE: This file is duplicated across mlbrgn packages.
// If you update it, sync changes across packages.

const globalLoadedScripts = new Set();
const globalLoadedStyles = new Set();

/**
 * Create a package-scoped loader
 */
export function createAssetLoader(namespace, { globalDedup = false, basePath = '/vendor/mlbrgn/laravel-form-components'  } = {}) {
    console.log('mle createAssetLoader')

    const loadedScripts = new Set();
    const loadedStyles = new Set();

    function shouldSkip(set, globalSet, key, globalKey) {
        if (set.has(key)) return true;
        if (globalDedup && globalSet.has(globalKey)) return true;
        return false;
    }

    function markLoaded(set, globalSet, key, globalKey) {
        console.log(key, 'loaded')

        set.add(key);
        if (globalDedup) globalSet.add(globalKey);
    }

    function resolveUrl(path) {
        if (/^(https?:)?\/\//.test(path)) {
            return path;
        }

        return `${basePath}/${path}`;
    }

    function loadScript(src, { type = 'module', async = false } = {}) {
        const fullSrc = resolveUrl(src);
        console.log('mle: loadScript ', fullSrc)

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
        loadStyle
    };
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
