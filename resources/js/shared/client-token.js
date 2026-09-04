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
            // Attempt to pick up token from cookie
            const match = document.cookie.match(/mle_client_token=([^;]+)/);
            if (match) token = match[1];
        }

        if (!token) {
            // Attempt to pick up token from page (pre-filled by server)
            const el = document.querySelector('[data-mle-client-token]');
            if (el) {
                token = el.value || el.getAttribute('data-mle-client-token');
            }
        }

        if (!token) {
            token = this.generateUlid();
            localStorage.setItem('mle_client_token', token);
            // Also set a long-lived cookie for server-side fallback
            document.cookie = `mle_client_token=${token}; path=/; max-age=${60 * 60 * 24 * 365 * 10}; SameSite=Lax`;

            // Sync all elements on page that might need this token
            document.dispatchEvent(new CustomEvent('mle:client-token-generated', { detail: { token } }));
        } else {
            // Ensure it's in localStorage if it was picked up from cookie or page
            if (localStorage.getItem('mle_client_token') !== token) {
                localStorage.setItem('mle_client_token', token);
            }
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

document.addEventListener('keypress', (e) => {
    console.log('key pressed: ', e)
})
