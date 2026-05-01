import fs from 'fs';
import { request } from '@playwright/test';

export default async function globalTeardown() {
  const context = await request.newContext({ baseURL: 'http://localhost' });

  await context.get('/__e2e__/cleanup-snapshots');

  if (fs.existsSync('.env.backup')) {
    fs.renameSync('.env', '.env.e2e');
    fs.renameSync('.env.backup', '.env');
  }

  await context.post('/__e2e__/artisan', { data: { command: 'config:clear', parameters: {} } });
  await context.post('/__e2e__/artisan', { data: { command: 'cache:clear', parameters: {} } });

  await context.dispose();
}
