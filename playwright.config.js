import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir:        './tests/e2e/tests',
  fullyParallel:  false,
  workers:        1,
  retries:        3,
  globalSetup:    './tests/e2e/global-setup.js',
  globalTeardown: './tests/e2e/global-teardown.js',
  outputDir:      'tests/e2e/test-results',
  use: {
    baseURL:    'http://localhost',
    screenshot: 'only-on-failure',
    trace:      'on-first-retry',
  },
  projects: [
    { name: 'firefox',  use: { ...devices['Desktop Firefox'] } },
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    { name: 'webkit',   use: { ...devices['Desktop Safari'] } },
  ],
});
