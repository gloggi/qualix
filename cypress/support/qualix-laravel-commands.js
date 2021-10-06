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
