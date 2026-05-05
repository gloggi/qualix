import fs from 'fs';
import { request } from '@playwright/test';

export default async function globalSetup() {
  if (fs.existsSync('.env.e2e')) {
    fs.renameSync('.env', '.env.backup');
    fs.copyFileSync('.env.e2e', '.env');
  }

  const context = await request.newContext({ baseURL: 'http://localhost' });

  await context.post('/__e2e__/artisan', { data: { command: 'config:clear', parameters: {} } });
  await context.post('/__e2e__/artisan', { data: { command: 'cache:clear', parameters: {} } });

  await context.dispose();
}
