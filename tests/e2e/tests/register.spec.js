import { test, expect } from '../fixtures/index.js';

test.describe('register page', () => {
  test.beforeEach(async ({ page, logout }) => {
    await logout();
    await page.goto('/register');
  });

  test('displays the page title', async ({ page }) => {
    await expect(page.locator('.card-header')).toContainText('Neu registrieren');
  });

  test('displays the login link', async ({ page }) => {
    const link = page.locator('form').getByText('Ich habe schon einen Account');
    await expect(link).toHaveAttribute('href', /\/login$/);
  });

  test('requires name', async ({ page }) => {
    await expect(page.locator('#name')).toHaveAttribute('required', '');
  });

  test('requires email', async ({ page }) => {
    await expect(page.locator('#email')).toHaveAttribute('required', '');
  });

  test('requires password', async ({ page }) => {
    await expect(page.locator('#password')).toHaveAttribute('required', '');
  });

  test('requires password confirmation', async ({ page }) => {
    await expect(page.locator('#password-confirm')).toHaveAttribute('required', '');
  });

  test('displays the MiData registration button', async ({ page }) => {
    await expect(page.getByText('Via PBS MiData registrieren')).toHaveAttribute('href', /\/login\/hitobito$/);

    const response = await page.request.fetch('/login/hitobito', { maxRedirects: 0 });
    expect(response.status()).toBe(302);
    expect(response.headers()['location']).toMatch(/^http:\/\/e2e-mocks:1080\/oauth\/authorize\?client_id=xxx/);
  });

  test.describe('classic registration', () => {
    test('registers and verifies email successfully', async ({ page, generate, lastSentMail }) => {
      const user = await generate('App\\Models\\User');
      await page.locator('#name').fill(user.name);
      await page.locator('#email').fill(user.email);
      await page.locator('#password').fill('password');
      await page.locator('#password-confirm').fill('password');
      await page.keyboard.press('Enter');

      await expect(page).toHaveURL(/\/email\/verify$/);
      await expect(page.locator('.card-header')).toContainText('E-Mail-Adresse verifizieren');
      await expect(page.getByText(`Du kannst den Link in deinen E-Mails unter ${user.email} zur Verifizierung verwenden.`)).toBeVisible();

      const mail = await lastSentMail();
      const verifyLink = mail.match(/http:\/\/localhost\/email\/verify\/\S+/)[0];

      await page.goto(verifyLink);
      await expect(page.getByText('Willkommä bim Qualix')).toBeVisible();
      await expect(page.getByText('Du bist momentan noch in keinem Kurs eingetragen.')).toBeVisible();
    });

    test('validates existing email when registering', async ({ page, create }) => {
      const user = await create('App\\Models\\User', 1, { name: 'Test Account' });
      await page.locator('#name').fill('Bari');
      await page.locator('#email').fill(user.email);
      await page.locator('#password').fill('password');
      await page.locator('#password-confirm').fill('password');
      await page.keyboard.press('Enter');

      await expect(page.getByText('E-Mail-Adresse ist schon vergeben.')).toBeVisible();
    });
  });

  test.describe('MiData registration', () => {
    test.beforeEach(async ({ resetMocks }) => {
      await resetMocks();
    });

    test('logs in successfully', async ({ page, mockMiDataOAuth }) => {
      await mockMiDataOAuth();

      const stateResponse = await page.request.fetch('/login/hitobito', { maxRedirects: 0 });
      const state = stateResponse.headers()['location'].match(/[&?]state=([^&]+)/)[1];

      await page.goto(`/login/hitobito/callback?code=foo&state=${state}`);
      await expect(page.getByText('Willkommä bim Qualix')).toBeVisible();
      await expect(page).toHaveURL(/\/$/);
    });

    test('fails gracefully when MiData is down', async ({ page }) => {
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
