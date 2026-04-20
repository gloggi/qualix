import { test, expect } from '../fixtures/index.js';

test.describe('requirements matrix', () => {
  test.beforeEach(async ({ login, artisan }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
  });

  test('can be displayed and edited', async ({ page, create, php, courseId: getCourseId }) => {
    const courseId = await getCourseId();

    await create('App\\Models\\RequirementStatus', 1, {
      course_id: courseId,
      name: 'E2E status',
      color: 'green',
      icon: 'book',
    });

    const feedback = await php(`App\\Models\\FeedbackData::orderBy('id', 'desc')->first();`);

    await page.goto(`/course/${courseId}`);
    await page.getByText(feedback.name).click();
    await expect(page.getByText('Anforderungs-Matrix')).toBeVisible();
    await expect(page.getByText(`Anforderungs-Matrix ${feedback.name}`)).toBeVisible();

    const cell = page.locator('[data-label]').nth(1);
    await cell.click();
    await page.getByRole('dialog').getByRole('combobox', { name: 'Status' }).click();
    await page.getByRole('dialog').locator('#requirement-status .multiselect__option').nth(3).click();
    await page.getByRole('dialog').locator('#comment').clear();
    await page.getByRole('dialog').locator('#comment').pressSequentially('Test E2E Notes which should be truncated because they are way too long to fit into the matrix cell');
    await expect(page.getByRole('dialog').getByText('Automatisch gespeichert')).toBeVisible();

    await page.getByRole('dialog').getByRole('button', { name: 'Close' }).click();
    await expect(page.getByRole('dialog').getByText('Automatisch gespeichert')).not.toBeVisible();
    await expect(page.getByText('Test E2E Notes which should be truncated because…')).toBeVisible();
    await expect(cell).toHaveClass(/bg-green/);
  });

  test('can be displayed and edited if there are multiple feedbacks in course', async ({ page, create, php, courseId: getCourseId }) => {
    const courseId = await getCourseId();

    await create('App\\Models\\RequirementStatus', 1, {
      course_id: courseId,
      name: 'E2E status',
      color: 'green',
      icon: 'book',
    });
    await create('App\\Models\\FeedbackData', 1, { course_id: courseId, name: 'E2E Feedback' });

    const feedback = await php(`App\\Models\\FeedbackData::orderBy('id', 'desc')->offset(1)->first();`);

    await page.goto(`/course/${courseId}`);
    await page.getByRole('button', { name: 'Rückmeldungen' }).click();
    await expect(page.getByText('E2E Feedback')).toBeVisible();
    await page.getByText(feedback.name).click();
    await expect(page.getByText('Anforderungs-Matrix')).toBeVisible();
    await expect(page.getByText(`Anforderungs-Matrix ${feedback.name}`)).toBeVisible();

    const cell = page.locator('[data-label]').nth(1);
    await cell.click();
    await page.getByRole('dialog').getByRole('combobox', { name: 'Status' }).click();
    await page.getByRole('dialog').locator('#requirement-status .multiselect__option').nth(3).click();
    await page.getByRole('dialog').locator('#comment').clear();
    await page.getByRole('dialog').locator('#comment').pressSequentially('Test E2E Notes which should be truncated because they are way too long to fit into the matrix cell');
    await expect(page.getByRole('dialog').getByText('Automatisch gespeichert')).toBeVisible();

    await page.getByRole('dialog').getByRole('button', { name: 'Close' }).click();
    await expect(page.getByRole('dialog').getByText('Automatisch gespeichert')).not.toBeVisible();
    await expect(page.getByText('Test E2E Notes which should be truncated because…')).toBeVisible();
    await expect(cell).toHaveClass(/bg-green/);
  });

  test('edits and prints all feedbacks', async ({ page, create, php, courseId: getCourseId, waitForDownload }) => {
    const courseId = await getCourseId();

    await create('App\\Models\\RequirementStatus', 1, {
      course_id: courseId,
      name: 'E2E status',
      color: 'green',
      icon: 'book',
    });

    const feedback = await php(`App\\Models\\FeedbackData::orderBy('id', 'desc')->first();`);

    await page.goto(`/course/${courseId}`);
    await page.getByText(feedback.name).click();
    await expect(page.getByText('Anforderungs-Matrix')).toBeVisible();
    await expect(page.getByText(`Anforderungs-Matrix ${feedback.name}`)).toBeVisible();

    await page.locator('[data-original-title="Drucken"]').first().click();

    const zipBuffer = await waitForDownload();
    expect(zipBuffer.length).toBeGreaterThan(0);
  });
});
