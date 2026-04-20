import { test, expect } from '../fixtures/index.js';
import pdfParse from 'pdf-parse/lib/pdf-parse.js';

test.describe('evaluation grids', () => {
  let courseId;

  test.beforeEach(async ({ login, artisan, courseId: getCourseId }) => {
    const user = await login();
    await artisan('e2e:scenario', { '--user-id': user.id });
    courseId = await getCourseId();
  });

  test('deletes, creates and prints evaluation grid templates, and fills in an evaluation grid', async ({ page, waitForDownload }) => {
    await page.goto(`/course/${courseId}/admin/evaluation_grids`);

    await page.locator('.fa-circle-minus').last().click();
    await page.locator('.modal.show').getByRole('button', { name: 'Löschen' }).click();

    await page.locator('.fa-circle-minus').last().click();
    await page.locator('.modal.show').getByRole('button', { name: 'Löschen' }).click();

    await page.locator('[autofocus]').fill('End-to-end test evaluation grid');
    await page.keyboard.press('Enter');

    await page.getByRole('combobox', { name: 'Leistungszeitpunkte' }).click();
    await page.locator('.form-control-multiselect#blocks .multiselect__option').first().click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.getByRole('combobox', { name: 'Anforderungen' }).click();
    await page.locator('.form-control-multiselect#requirements .multiselect__option').first().click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.locator('#row-templates-0-criterion').fill('End-to-end test criterion');
    await page.locator('.form-control-multiselect#row-templates-0-control-type').click();
    await page.locator('.form-control-multiselect#row-templates-0-control-type .multiselect__option').filter({ hasText: 'Zwischentitel' }).click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.getByText('Zeile hinzufügen').click();
    await page.getByText('Zeile hinzufügen').click();
    await page.getByText('Zeile hinzufügen').click();

    await page.locator('#row-templates-1-criterion').fill('End-to-end test scale');
    await page.locator('.form-control-multiselect#row-templates-1-control-type').click();
    await page.locator('.form-control-multiselect#row-templates-1-control-type .multiselect__option').filter({ hasText: 'Skala' }).click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.locator('#row-templates-2-criterion').fill('End-to-end test radio buttons');
    await page.locator('.form-control-multiselect#row-templates-2-control-type').click();
    await page.locator('.form-control-multiselect#row-templates-2-control-type .multiselect__option').filter({ hasText: '✨' }).click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.locator('#row-templates-3-criterion').fill('End-to-end test checkbox');
    await page.locator('.form-control-multiselect#row-templates-3-control-type').click();
    await page.locator('.form-control-multiselect#row-templates-3-control-type .multiselect__option').filter({ hasText: 'Checkbox' }).click();
    await page.locator('.card-body').first().click({ position: { x: 900, y: 5 } });

    await page.getByRole('button', { name: 'Erstellen' }).click();
    await expect(page.getByText("Das Beurteilungsraster 'End-to-end test evaluation grid' wurde erfolgreich erstellt.")).toBeVisible();

    await page.locator('td').locator('[data-original-title="Drucken"]').click();

    let parsed = await pdfParse(await waitForDownload());
    expect(parsed.text).toContain('End-to-end test evaluation grid');
    expect(parsed.text).toContain('End-to-end test criterion');
    expect(parsed.text).toContain('End-to-end test scale');
    expect(parsed.text).toContain('End-to-end test radio buttons');
    expect(parsed.text).toContain('End-to-end test checkbox');
    expect(parsed.text).not.toContain('Some other text which certainly is not present in the pdf');

    await page.goto(`/course/${courseId}/participants`);
    await page.getByText('Beobachtung erfassen').first().click();

    await page.getByRole('combobox', { name: 'Block' }).click();
    await page.locator('.form-control-multiselect#block .multiselect__option').first().click();

    await page.getByText('End-to-end test evaluation grid').click();
    await page.locator('textarea').first().fill('foobar e2e test');
    await page.getByText('Speichern').click();
    await expect(page.getByText('Beurteilungsraster erfasst.')).toBeVisible();
    await page.locator('a i.fa-arrow-right').click();

    await page.getByRole('row', { name: 'End-to-end test evaluation grid' }).locator('td').locator('[data-original-title="Drucken"]').click();

    parsed = await pdfParse(await waitForDownload());
    expect(parsed.text).toContain('End-to-end test evaluation grid');
    expect(parsed.text).toContain('End-to-end test criterion');
    expect(parsed.text).toContain('End-to-end test scale');
    expect(parsed.text).toContain('End-to-end test radio buttons');
    expect(parsed.text).toContain('End-to-end test checkbox');
    expect(parsed.text).toContain('foobar e2e test');
    expect(parsed.text).not.toContain('Some other text which certainly is not present in the pdf');
  });
});
