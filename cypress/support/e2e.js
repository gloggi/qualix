// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

import './commands'
import './mail'
import './midata-oauth'
import './laravel-commands'
import './qualix-laravel-commands'
import './laravel-routes'
import './assertions'

before(() => {
  cy.task('activateCypressEnvFile', {}, {log: false})
  cy.artisan('config:clear', {}, {log: false})
  cy.artisan('cache:clear', {}, {log: false})

  cy.refreshRoutes();
})

after(() => {
  cy.task('activateLocalEnvFile', {}, {log: false})
  cy.artisan('config:clear', {}, {log: false})
  cy.artisan('cache:clear', {}, {log: false})
})

Cypress.on("window:before:load", win => {
  cy.stub(win.console, "error").callsFake(msg => {
    cy.now("task", "Qualix logged an error to the console, please review the above error message", msg)
    throw new Error(msg)
  })
})
