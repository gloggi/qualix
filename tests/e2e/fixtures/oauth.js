import { test as base } from '@playwright/test';

export const test = base.extend({

  resetMocks: async ({ request }, use) => {
    await use(async () => {
      await request.fetch('http://localhost:1090/mockserver/reset', { method: 'PUT' });
    });
  },

  mockMiDataOAuth: async ({ request }, use) => {
    await use(async () => {
      await request.fetch('http://localhost:1090/mockserver/expectation', {
        method: 'PUT',
        data: {
          id: 'midata-oauth-token-endpoint',
          httpRequest: { method: 'POST', path: '/oauth/token' },
          httpResponse: {
            statusCode: 200,
            headers: {},
            body: {
              access_token: 'blabla',
              token_type: 'Bearer',
              expires_in: 7200,
              scope: 'name',
              created_at: 1598729772,
            },
          },
        },
      });
      await request.fetch('http://localhost:1090/mockserver/expectation', {
        method: 'PUT',
        data: {
          id: 'midata-oauth-profile-endpoint',
          httpRequest: { method: 'GET', path: '/oauth/profile' },
          httpResponse: {
            statusCode: 200,
            headers: {},
            body: {
              id: '1234',
              nickname: 'OAuther',
              email: 'oauther@email.com',
            },
          },
        },
      });
    });
  },

  mockMiDataDown: async ({ request }, use) => {
    await use(async () => {
      await request.fetch('http://localhost:1090/mockserver/expectation', {
        method: 'PUT',
        data: {
          id: 'midata-oauth-token-endpoint-down',
          httpRequest: { method: 'POST', path: '/oauth/token' },
          httpError: {
            dropConnection: true,
          },
        },
      });
      await request.fetch('http://localhost:1090/mockserver/expectation', {
        method: 'PUT',
        data: {
          id: 'midata-oauth-profile-endpoint-down',
          httpRequest: { method: 'GET', path: '/oauth/profile' },
          httpError: {
            dropConnection: true,
          },
        },
      });
    });
  },
});
