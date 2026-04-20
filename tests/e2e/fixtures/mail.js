import { test as base } from '@playwright/test';
import quotedPrintable from 'quoted-printable';

export const test = base.extend({

  lastSentMail: async ({ request }, use) => {
    await use(async () => {
      const listResponse = await request.get('http://localhost:1080/messages');
      const messages = await listResponse.json();
      if (!messages || messages.length === 0) {
        throw new Error('No emails have been sent');
      }
      const lastMsg = messages[messages.length - 1];
      const detailResponse = await request.get(
        `http://localhost:1080/messages/${lastMsg.id}.json`
      );
      const detail = await detailResponse.json();
      return quotedPrintable.decode(detail.source);
    });
  },

});
