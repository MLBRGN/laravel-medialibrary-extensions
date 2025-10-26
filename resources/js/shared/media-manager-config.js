export function getMediaManagerConfig(mediaManager) {
    const configInput = mediaManager.querySelector('[data-media-manager-config]');
    if (!configInput) return null;

    try {
        return JSON.parse(configInput.value);
    } catch (e) {
        console.error(`Invalid JSON config for ${mediaManager.id}:`, e);
        return null;
    }
}
