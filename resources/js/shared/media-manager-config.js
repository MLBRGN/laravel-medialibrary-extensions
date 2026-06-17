import { ClientToken } from './client-token';

export function getMediaManagerConfig(mediaManager) {
    const configInput = mediaManager.querySelector('[data-mle-media-manager-config]');
    if (!configInput) return null;

    try {
        const config = JSON.parse(configInput.value);

        // Ensure clientToken matches our persistent client token
        config.clientToken = ClientToken.get();

        return config;
    } catch (e) {
        console.error(`Invalid JSON config for ${mediaManager.id}:`, e);
        return null;
    }
}
