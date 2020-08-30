// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

Cypress.Commands.add("mockMiDataOAuth", () => {
  cy.request('PUT', 'http://e2e-mocks:1080/mockserver/expectation', JSON.stringify({
    "id": "midata-oauth-token-endpoint",
    "httpRequest": {
      "method": "POST",
      "path": "/oauth/token"
    },
    "httpResponse": {
      "statusCode": 200,
      "headers": {},
      "body": {
        "access_token": "blabla",
        "token_type": "Bearer",
        "expires_in": 7200,
        "scope": "name",
        "created_at": 1598729772
      }
    }
  }))
  cy.request('PUT', 'http://e2e-mocks:1080/mockserver/expectation', JSON.stringify({
    "id": "midata-oauth-profile-endpoint",
    "httpRequest": {
      "method": "GET",
      "path": "/oauth/profile"
    },
    "httpResponse": {
      "statusCode": 200,
      "headers": {},
      "body": {
        "id": "1234",
        "nickname": "OAuther",
        "email": "oauther@email.com"
      }
    }
  }))
}, { prevSubject: false })

Cypress.Commands.add("resetMocks", () => {
  cy.request('PUT', 'http://e2e-mocks:1080/mockserver/reset')
}, { prevSubject: false })
