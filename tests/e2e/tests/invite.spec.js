import { test, expect } from '../fixtures/index.js';

test.describe('invitation flow', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test.beforeEach(async ({ page }) => {
    await page.goto(`/course/${courseId}/admin/equipe`);
  });

  test('displays the page title', async ({ page }) => {
    await expect(page.getByText('Equipenmitglied einladen')).toBeVisible();
  });

  test('requires email', async ({ page }) => {
    await expect(page.locator('#email')).toHaveAttribute('required', '');
  });

  test('validates email', async ({ page }) => {
    await page.locator('#email').fill('something-that-is-not-an-email');
    await page.keyboard.press('Enter');
    await expect(page.getByText('E-Mail muss eine gültige E-Mail-Adresse sein.')).toBeVisible();
  });

  test('successfully invites another equipe member', async ({ page, login, lastSentMail }) => {
    await page.locator('#email').fill('some-email@gmail.com');
    await page.getByRole('button', { name: 'Einladen' }).click();
    await expect(page.getByText('Wir haben eine Einladung an some-email@gmail.com gesendet.')).toBeVisible();

    await page.request.post('/__e2e__/logout');
    await login({ email: 'some-email@gmail.com' });

    const mail = await lastSentMail();
    const verifyLink = mail.match(/http:\/\/localhost\/invitation\/[a-zA-Z0-9]+/)[0];

    await page.goto(verifyLink);
    await expect(page.locator('.card-header')).toContainText('Einladung in ');
    await expect(page.getByText('Gehört dir die Mailadresse some-email@gmail.com?')).toBeVisible();
    await expect(page.getByText('Nein, diese Einladung ist nicht für mich')).toHaveAttribute('href', /^http:\/\/localhost$/);

    await page.getByText('Ja, Einladung annehmen').click();
    await expect(page.getByText('Einladung angenommen. Du bist jetzt in der Kursequipe von ')).toBeVisible();
    await expect(page.getByText('Willkommä bim Qualix')).toBeVisible();
    await expect(page.getByText('Qualix soll gegen den Papier-Krieg helfen')).toBeVisible();
  });

  test('deletes an invitation', async ({ page }) => {
    await page.locator('#email').fill('some-email@gmail.com');
    await page.getByRole('button', { name: 'Einladen' }).click();
    await expect(page.getByText('Wir haben eine Einladung an some-email@gmail.com gesendet.')).toBeVisible();

    await page.locator('.card').filter({ hasText: 'Einladungen' }).locator('[title="Löschen"]').click();
    await page.getByText('Willst du die Einladung für some-email@gmail.com wirklich entfernen?')
      .locator('..')
      .getByRole('button', { name: 'Löschen' })
      .click();

    await expect(page.getByText('Die Einladung für some-email@gmail.com wurde erfolgreich gelöscht.')).toBeVisible();
    await expect(page.getByText('Momentan sind keine Einladungen offen.')).toBeVisible();
  });
});
