import { test as base } from '@playwright/test';

export const test = base.extend({
  /**
   * Intercepts client-side blob downloads (e.g. from file-saver) which don't
   * trigger Playwright's download event. Call the returned function after the
   * download completes to get the file as a Buffer.
   */
  waitForDownload: async ({ page }, use) => {
    let nextResolve = null;
    const queue = [];

    await page.exposeFunction('__captureDownload', (base64) => {
      const buffer = Buffer.from(base64, 'base64');
      if (nextResolve) {
        const resolve = nextResolve;
        nextResolve = null;
        resolve(buffer);
      } else {
        queue.push(buffer);
      }
    });

    await page.addInitScript(() => {
      const origCreateObjectURL = URL.createObjectURL;
      URL.createObjectURL = function(obj) {
        const url = origCreateObjectURL.call(URL, obj);
        if (obj instanceof Blob && (obj.type === 'application/pdf' || obj.type === 'application/zip')) {
          const reader = new FileReader();
          reader.onloadend = () => window.__captureDownload(reader.result.split(',')[1]);
          reader.readAsDataURL(obj);
        }
        return url;
      };
    });

    const waitForDownload = () =>
      queue.length > 0
        ? Promise.resolve(queue.shift())
        : new Promise(resolve => { nextResolve = resolve; });

    await use(waitForDownload);
  },
});
