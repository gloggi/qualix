import { test, expect } from '../fixtures/index.js';

test.describe('participant group generator', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test('generates and saves some participant groups', async ({ page }) => {
    await page.goto(`/course/${courseId}/admin/participantGroups`);
    await page.getByText('TN-Gruppen-Generator').click();
    await page.getByText('Gruppenvorschlag generieren').click();

    await expect(page.locator('#participant-groups-0-0-group-name')).toHaveValue('Arbeitsgruppe 1');
    await page.locator('#participant-groups-0-0-group-name').clear();
    await page.locator('#participant-groups-0-0-group-name').fill('E2E test group');
    await expect(page.locator('#participant-groups-0-2-group-name')).toHaveValue('Arbeitsgruppe 3');

    await page.getByText('Speichern').click();
    await expect(page.getByText('TN-Gruppen wurden erfolgreich erstellt')).toBeVisible();
    await expect(page.getByText('E2E test group')).toBeVisible();
  });
});
