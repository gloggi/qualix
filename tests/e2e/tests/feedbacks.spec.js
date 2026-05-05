import { test, expect } from '../fixtures/index.js';
import pdfParse from 'pdf-parse/lib/pdf-parse.js';

test.describe('feedback editor', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test('creates a feedback', async ({ page }) => {
    await page.goto(`/course/${courseId}/admin/feedbacks`);
    await page.locator('[autofocus]').fill('End-to-end test feedback');
    await page.keyboard.press('Enter');
    await expect(page.getByText("Die Rückmeldung 'End-to-end test feedback' wurde erfolgreich erstellt.")).toBeVisible();
  });

  test('edits and prints a feedback', async ({ page, waitForDownload }) => {
    await page.goto(`/course/${courseId}/participants`);
    await page.locator('img.card-img-top').first().click();
    await page.locator('td[data-label=Titel] a').first().click();
    await expect(page.getByText('Rückmeldung Details')).toBeVisible();

    await page.locator('div.editor.form-control [contenteditable]').first().pressSequentially('\n\nText from end-to-end test\n\n');
    await expect(page.getByText('Automatisch gespeichert')).not.toBeVisible();
    await expect(page.getByText('Warte bis fertig getippt...')).not.toBeVisible();
    await expect(page.getByText('Automatisch gespeichert')).toBeVisible();
    await page.locator('a.btn-link').first().click();

    await page.locator('div.card').filter({ hasText: 'Rückmeldungen' }).locator('[data-original-title="Drucken"]').click();

    const parsed = await pdfParse(await waitForDownload());
    expect(parsed.text).toContain('Text from end-to-end test');
    expect(parsed.text).not.toContain('Some other text which certainly is not present in the pdf');
  });
});
