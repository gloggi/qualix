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
