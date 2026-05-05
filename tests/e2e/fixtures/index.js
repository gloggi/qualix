import { mergeTests, expect } from '@playwright/test';
import { test as laravelTest } from './laravel.js';
import { test as mailTest }    from './mail.js';
import { test as oauthTest }   from './oauth.js';
import { test as dbTest }      from './db.js';
import { test as downloadTest } from './download.js';

export { expect };
export const test = mergeTests(laravelTest, mailTest, oauthTest, dbTest, downloadTest);
