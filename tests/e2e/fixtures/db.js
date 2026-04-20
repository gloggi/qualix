import { test as base } from '@playwright/test';

export const test = base.extend({

  createDBSnapshot: async ({ request }, use) => {
    await use(async (name = 'e2e_snapshot') => {
      await request.get(`/__e2e__/create-snapshot/${name}`);
    });
  },

  restoreDBSnapshot: async ({ request }, use) => {
    await use(async (name = 'e2e_snapshot') => {
      await request.get(`/__e2e__/restore-snapshot/${name}`);
    });
  },

  cleanupDBSnapshots: async ({ request }, use) => {
    await use(async () => {
      await request.get('/__e2e__/cleanup-snapshots');
    });
  },

  useDatabaseResets: [async ({ request }, use, testInfo) => {
    const name = testInfo.testId
      .replace(/[^a-z0-9]/gi, '_')
      .substring(0, 50);
    await request.get(`/__e2e__/create-snapshot/${name}`);
    await use();
    await request.get(`/__e2e__/restore-snapshot/${name}`);
  }, { auto: true, scope: 'test' }],

});
