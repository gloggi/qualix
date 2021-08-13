/**
 * Create a new user and log them in.
 *
 * @param {Object} attributes
 *
 * @example cy.login();
 *          cy.login({ name: 'JohnDoe' });
 */
Cypress.Commands.add('login', (attributes = {}) => {
  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/login',
        body: {attributes, _token: token},
        log: false
      })
    })
    .then(({body}) => {
      Cypress.log({
        name: 'login',
        message: attributes,
        consoleProps: () => ({user: body})
      })
    }).its('body', {log: false})
})

/**
 * Logout the current user.
 *
 * @example cy.logout();
 */
Cypress.Commands.add('logout', () => {
  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/logout',
        body: {_token: token},
        log: false
      })
    })
    .then(() => {
      Cypress.log({name: 'logout', message: ''})
    })
})

/**
 * Simulate the session expiring, so it doesn't contain anything anymore.
 *
 * @example cy.expireSession();
 */
Cypress.Commands.add('expireSession', () => {
  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/expire-session',
        body: {_token: token},
        log: false
      })
    })
    .then(() => {
      Cypress.log({name: 'expireSession', message: ''})
    })
})

/**
 * Fetch a CSRF token.
 *
 * @example cy.csrfToken();
 */
Cypress.Commands.add('csrfToken', () => {
  return cy
    .request({
      method: 'GET',
      url: '/__cypress__/csrf_token',
      log: false,
    })
    .its('body', {log: false})
})

/**
 * Create a new Eloquent factory.
 *
 * @param {String} model
 * @param {Number|null} times
 * @param {Object} attributes
 *
 * @example cy.create('App\\User');
 *          cy.create('App\\User', 2);
 *          cy.create('App\\User', 2, { active: false });
 */
Cypress.Commands.add('create', (model, times = null, attributes = {}) => {
  if (typeof times === 'object' && times !== null) {
    attributes = times
    times = null
  }

  return cy
    .csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/factory',
        body: {attributes, model, times, _token: token},
        log: false
      })
    })
    .then(response => {
      Cypress.log({
        name: 'create',
        message: model + (times ? `(${times} times)` : ''),
        consoleProps: () => ({[model]: response.body})
      })
    })
    .its('body', {log: false})
})

/**
 * Generate a new Eloquent model, but do not persist it to the database.
 *
 * @param {String} model
 * @param {Number|null} times
 * @param {Object} attributes
 *
 * @example cy.generate('App\\User');
 *          cy.generate('App\\User', 2);
 *          cy.generate('App\\User', 2, { active: false });
 */
Cypress.Commands.add('generate', (model, times = null, attributes = {}) => {
  if (typeof times === 'object' && times !== null) {
    attributes = times
    times = null
  }

  return cy
    .csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/generate',
        body: {attributes, model, times, _token: token},
        log: false
      })
    })
    .its('body', {log: false})
})

/**
 * Refresh the database state.
 *
 * @param {Object} options
 *
 * @example cy.refreshDatabase();
 *          cy.refreshDatabase({ '--drop-views': true });
 */
Cypress.Commands.add('refreshDatabase', (options = {}) => {
  return cy.artisan('migrate:fresh', options)
})

/**
 * Seed the database.
 *
 * @param {String} seederClass
 *
 * @example cy.seed();
 *          cy.seed('PlansTableSeeder');
 */
Cypress.Commands.add('seed', (seederClass) => {
  return cy.artisan('db:seed', {
    '--class': seederClass,
  })
})

/**
 * Trigger an Artisan command.
 *
 * @param {String} command
 * @param {Object} parameters
 *
 * @example cy.artisan('cache:clear');
 */
Cypress.Commands.add('artisan', (command, parameters = {}) => {
  if (parameters.log) {
    Cypress.log({
      name: 'artisan',
      message: command,
      consoleProps: () => ({command, parameters})
    })
  }

  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/artisan',
        body: {command: command, parameters: parameters, _token: token},
        log: false
      })
    })
})

/**
 * Execute arbitrary PHP.
 *
 * @param {String} command
 *
 * @example cy.php('2 + 2');
 *          cy.php('App\\User::count());
 */
Cypress.Commands.add('php', command => {
  return cy
    .csrfToken()
    .then((token) => {
      return cy.request({
        method: 'POST',
        url: '/__cypress__/run-php',
        body: {command: command, _token: token},
        log: false
      })
    })
    .then(response => {
      Cypress.log({
        name: 'php',
        message: command,
        consoleProps: () => ({result: response.body.result})
      })
    })
    .its('body.result', {log: false})
})


/**
 * Create a restorable database snapshot.
 *
 * @param {String} name
 * @param {Object} parameters
 *
 * @example cy.createSnapshot();
 */
Cypress.Commands.add('createDBSnapshot', (name = '', parameters = {}) => {
  if (typeof name === 'object') {
    parameters = name
    name = ''
  }

  if (parameters.log) {
    Cypress.log({
      name: 'createSnapshot',
      consoleProps: () => ({name, parameters})
    })
  }

  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'GET',
        url: `/__cypress__/create-snapshot/${name}`,
        body: {parameters: parameters, _token: token},
        log: false
      })
        .its('body', {log: false})
    })
})

/**
 * Restore a database snapshot.
 *
 * @param {String} name
 * @param {Object} parameters
 *
 * @example cy.restoreSnapshot();
 */
Cypress.Commands.add('restoreDBSnapshot', (name = '', parameters = {}) => {
  if (typeof name === 'object') {
    parameters = name
    name = ''
  }

  if (parameters.log) {
    Cypress.log({
      name: 'restoreSnapshot',
      consoleProps: () => ({name, parameters})
    })
  }

  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'GET',
        url: `/__cypress__/restore-snapshot/${name}`,
        body: {parameters: parameters, _token: token},
        log: false
      })
    })
})

/**
 * Clean up all database snapshots.
 *
 * @param {Object} parameters
 *
 * @example cy.cleanupSnapshots();
 */
Cypress.Commands.add('cleanupDBSnapshots', (parameters = {}) => {
  if (parameters.log) {
    Cypress.log({
      name: 'cleanupSnapshots',
      consoleProps: () => ({parameters})
    })
  }

  return cy.csrfToken()
    .then(token => {
      return cy.request({
        method: 'GET',
        url: '/__cypress__/cleanup-snapshots',
        body: {parameters: parameters, _token: token},
        log: false
      })
    })
})
