import { test as base, expect } from '@playwright/test';
import fs from 'fs';

export const test = base.extend({

  login: async ({ page }, use) => {
    await use(async (attributes = {}) => {
      const body = (attributes.attributes || attributes.state || attributes.load)
        ? attributes
        : { attributes };
      const response = await page.request.post('/__e2e__/login', { data: body });
      return response.json();
    });
  },

  logout: async ({ page }, use) => {
    await use(async () => {
      await page.request.post('/__e2e__/logout');
    });
  },

  create: async ({ request }, use) => {
    await use(async (model, count = 1, attributes = {}, load = [], state = []) => {
      let requestBody;
      if (typeof model !== 'object') {
        if (Array.isArray(count)) {
          state = attributes; attributes = {}; load = count; count = 1;
        } else if (typeof count === 'object') {
          state = load; load = attributes; attributes = count; count = 1;
        }
        requestBody = { model, state, attributes, load, count };
      } else {
        requestBody = model;
      }
      const response = await request.post('/__e2e__/create', { data: requestBody });
      return response.json();
    });
  },

  generate: async ({ request }, use) => {
    await use(async (model, times = null, attributes = {}) => {
      if (typeof times === 'object' && times !== null) {
        attributes = times;
        times = null;
      }
      const response = await request.post('/__e2e__/generate', {
        data: { model, times, attributes },
      });
      return response.json();
    });
  },

  artisan: async ({ request }, use) => {
    await use(async (command, parameters = {}) => {
      await request.post('/__e2e__/artisan', { data: { command, parameters } });
    });
  },

  php: async ({ request }, use) => {
    await use(async (command) => {
      const response = await request.post('/__e2e__/run-php', { data: { command } });
      const body = await response.json();
      return body.result;
    });
  },

  courseId: async ({ page }, use) => {
    await use(async () => {
      await page.goto('/');
      const match = page.url().match(/\/course\/([0-9]+)/);
      if (!match) throw new Error('No course ID found in URL after navigating to /');
      return match[1];
    });
  },

  consoleErrorGuard: [async ({ page }, use) => {
    const errors = [];
    page.on('console', msg => {
      if (msg.type() === 'error') errors.push(msg.text());
    });
    await use();
    expect(errors, `Console errors detected:\n${errors.join('\n')}`).toHaveLength(0);
  }, { auto: true }],

});
