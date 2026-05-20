import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/Browser',
    fullyParallel: false,
    workers: 1,
    timeout: 60000,
    expect: {
        timeout: 5000,
    },
    reporter: './tests/Browser/clean-reporter.ts',
    use: {
        baseURL: process.env.APP_URL || 'http://127.0.0.1:8000',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
});
