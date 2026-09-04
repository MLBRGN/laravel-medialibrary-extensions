import { defineConfig } from '@playwright/test';

export default defineConfig({
    // ... your other config
    use: {
        headless: process.env.PEST_BROWSER_HEADED !== 'true',
        launchOptions: {
            // args: ['--auto-open-devtools-for-tabs'],
        },
    },
});
