/**
 * Manages a persistent client-side token (ULID).
 */
export const ClientToken = {
    /**
     * Get the existing client token or generate a new one.
     * @returns {string}
     */
    get() {
        let token = localStorage.getItem('mle_client_token');

        if (!token) {
            token = this.generateUlid();
            localStorage.setItem('mle_client_token', token);
            // Also set a long-lived cookie for server-side fallback
            document.cookie = `mle_client_token=${token}; path=/; max-age=${60 * 60 * 24 * 365 * 10}; SameSite=Lax`;
        }

        return token;
    },

    /**
     * Simple ULID-like generator.
     * @returns {string}
     */
    generateUlid() {
        const alphabet = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        let str = '';
        for (let i = 0; i < 26; i++) {
            str += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
        }
        return str;
    }
};
