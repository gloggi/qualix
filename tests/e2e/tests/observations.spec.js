import { test, expect } from '../fixtures/index.js';

test.describe('observation form', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test('creates an observation', async ({ page }) => {
    await page.goto(`/course/${courseId}/observation/new`);
    await expect(page.getByText('Beobachtung erfassen')).toBeVisible();

    await page.getByRole('combobox', { name: 'TN' }).click();
    await page.locator('#participants .multiselect__option').first().click();
    await page.locator('.card-body').click({ position: { x: 900, y: 5 } });

    await page.locator('#content').fill('hat sich mehrmals gut eingebracht');

    await page.getByRole('combobox', { name: 'Block' }).click();
    await page.locator('#block .multiselect__option').first().click();

    await page.getByRole('combobox', { name: 'Anforderungen' }).click();
    await page.locator('#requirements .multiselect__option').first().click();
    await page.locator('#requirements .multiselect__option').nth(2).click();
    await page.locator('.card-body').click({ position: { x: 900, y: 5 } });

    await page.getByText('Positiv').click();

    await expect(page.getByRole('combobox', { name: 'Beobachtet von' })).toBeVisible();
    await page.getByRole('combobox', { name: 'Beobachtet von' }).click();
    await page.locator('#users .multiselect__option:not(.multiselect__option--selected)').first().click();
    await page.locator('.card-body').click({ position: { x: 900, y: 5 } });

    await page.getByText('Speichern').click();

    await expect(page.getByText('Beobachtung erfasst.')).toBeVisible();
    await page.getByText('Zu ').click();
    await expect(page.getByText('hat sich mehrmals gut eingebracht')).toBeVisible();
  });
});
