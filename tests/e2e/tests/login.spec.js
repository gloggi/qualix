import { test, expect } from '../fixtures/index.js';

test.describe('login page', () => {
  test.beforeEach(async ({ page, logout }) => {
    await logout();
    await page.goto('/login');
  });

  test('displays the page title', async ({ page }) => {
    await expect(page.locator('.card-header')).toContainText('Anmelden');
  });

  test('displays the registration link', async ({ page }) => {
    const link = page.locator('form').getByText('Neu registrieren');
    await expect(link).toHaveAttribute('href', /\/register$/);
  });

  test('displays the password forgotten link', async ({ page }) => {
    await expect(page.getByText('Passwort vergessen?')).toHaveAttribute('href', /\/password\/reset$/);
  });

  test('requires email', async ({ page }) => {
    await expect(page.locator('#email')).toHaveAttribute('required', '');
  });

  test('requires password', async ({ page }) => {
    await expect(page.locator('#password')).toHaveAttribute('required', '');
  });

  test('displays the MiData login button', async ({ page }) => {
    await expect(page.getByText('Via PBS MiData einloggen')).toHaveAttribute('href', /\/login\/hitobito$/);

    const response = await page.request.fetch('/login/hitobito', { maxRedirects: 0 });
    expect(response.status()).toBe(302);
    expect(response.headers()['location']).toMatch(/^http:\/\/e2e-mocks:1080\/oauth\/authorize\?client_id=xxx/);
  });

  test.describe('classic login', () => {
    test('logs in successfully', async ({ page, create }) => {
      const user = await create('App\\Models\\User', 1, { name: 'Test Account' });
      await page.locator('#email').fill(user.email);
      await page.locator('#password').fill('password');
      await page.keyboard.press('Enter');
      await expect(page.getByText('Willkommä bim Qualix')).toBeVisible();
      await expect(page).toHaveURL(/\/$/);
    });

    test('validates wrong login', async ({ page }) => {
      await page.locator('#email').fill('someone-who-doesnt-exist@qualix.flamberg.ch');
      await page.locator('#password').fill('wrong');
      await page.keyboard.press('Enter');
      await expect(page.getByText('Dieses Login ist uns nicht bekannt. Meldest du dich vielleicht normalerweise mit MiData an?')).toBeVisible();
    });
  });

  test.describe('MiData login', () => {
    test.beforeEach(async ({ resetMocks }) => {
      await resetMocks();
    });

    test('logs in successfully', async ({ page, create, mockMiDataOAuth }) => {
      await mockMiDataOAuth();
      await create('App\\Models\\HitobitoUser', 1, { hitobito_id: '1234', email: 'oauther@email.com' });

      const stateResponse = await page.request.fetch('/login/hitobito', { maxRedirects: 0 });
      const state = stateResponse.headers()['location'].match(/[&?]state=([^&]+)/)[1];

      await page.goto(`/login/hitobito/callback?code=foo&state=${state}`);
      await expect(page.getByText('Willkommä bim Qualix')).toBeVisible();
      await expect(page).toHaveURL(/\/$/);
    });

    test('fails gracefully when MiData is down', async ({ page, mockMiDataDown }) => {
      await mockMiDataDown();
      const stateResponse = await page.request.fetch('/login/hitobito', { maxRedirects: 0 });
      const state = stateResponse.headers()['location'].match(/[&?]state=([^&]+)/)[1];

      await page.goto(`/login/hitobito/callback?code=foo&state=${state}`);
      await expect(page.getByText('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere dich mit einem klassischen Account.')).toBeVisible();
    });

    test('fails gracefully when permission on MiData not granted', async ({ page }) => {
      await page.goto('/login/hitobito/callback?code=foo&error=123');
      await expect(page.getByText('Zugriff in MiData verweigert.')).toBeVisible();
    });
  });
});
