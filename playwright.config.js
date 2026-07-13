import { defineConfig } from '@playwright/test';

export default defineConfig({
    // ... your other config
    use: {
        headless: false, // Forces headed mode globally for debug
        launchOptions: {
            args: ['--auto-open-devtools-for-tabs'],
        },
    },
});
