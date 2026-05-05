import { test, expect } from '../fixtures/index.js';

test.describe('name game', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test('plays through the easy version of the name game', async ({ page }) => {
    await page.goto(`/course/${courseId}/participants`);
    await page.getByText('Namen lernen').click();
    await expect(page.getByText('Name Game')).toBeVisible();

    await page.getByRole('combobox', { name: 'Namen' }).click();
    await page.locator('#participants .multiselect__option').first().click();
    await page.locator('#participants .multiselect__option').nth(2).click();
    await page.locator('#participants .multiselect__option').nth(3).click();
    await page.locator('.card-body').click({ position: { x: 900, y: 5 } });

    await page.getByText("Los geht's").click();
    await expect(page.getByText('Zeit:')).toBeVisible();

    for (let i = 0; i < 3; i++) {
      await page.locator('.name-game button[type=submit]').first().click();
      await page.locator('.name-game button[type=submit]').first().click();
    }

    await page.getByText('Nochmals').click();
    await expect(page.getByText("Los geht's")).toBeVisible();
  });

  test('plays through the hard version of the name game', async ({ page }) => {
    await page.goto(`/course/${courseId}/participants`);
    await page.getByText('Namen lernen').click();
    await expect(page.getByText('Name Game')).toBeVisible();

    await page.getByRole('combobox', { name: 'Namen' }).click();
    await page.locator('#participants .multiselect__option').first().click();
    await page.locator('#participants .multiselect__option').nth(2).click();
    await page.locator('#participants .multiselect__option').nth(3).click();
    await page.locator('.card-body').click({ position: { x: 900, y: 5 } });

    await page.getByRole('combobox', { name: 'Schwierigkeit' }).click();
    await page.getByText('Schwierig (Namen eintippen)').click();

    await page.getByText("Los geht's").click();
    await expect(page.getByText('Zeit:')).toBeVisible();

    for (let i = 0; i < 3; i++) {
      await page.locator('.name-game button[type=submit]').first().click();
      await page.locator('.name-game button[type=submit]').first().click();
    }

    await page.getByText('Nochmals').click();
    await expect(page.getByText("Los geht's")).toBeVisible();
    await expect(page.getByText('Einfach (multiple choice)')).not.toBeVisible();
  });
});
