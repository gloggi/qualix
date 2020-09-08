import { useDatabaseResets } from "../support/databaseTransactions"

describe('login page', () => {

  beforeEach(() => {
    cy.logout()
    cy.visit('/login')
  })

  it('displays the page title', () => {
    cy.get('.card-header').contains('Anmelden')
  })

  it('displays the registration link', () => {
    cy.get('form').contains('Neu registrieren')
      .should('have.attr', 'href').and('match', /\/register$/)
  })

  it('displays the password forgotten link', () => {
    cy.contains('Passwort vergessen?')
      .should('have.attr', 'href').and('match', /\/password\/reset$/)
  })

  it('requires email', () => {
    cy.get('#email')
      .should('have.attr', 'required')
  })

  it('requires password', () => {
    cy.get('#password')
      .should('have.attr', 'required')
  })

  it('displays the MiData login button', () => {
    cy.contains('Via PBS MiData einloggen')
      .should('have.attr', 'href').and('match', /\/login\/hitobito$/)

    cy.request({
      url: '/login/hitobito',
      followRedirect: false
    })
      .then((resp) => {
        expect(resp.status).to.eq(302)
        expect(resp.redirectedToUrl).to.match(/^http:\/\/e2e-mocks:1080\/oauth\/authorize\?client_id=xxx/)
      })
  })

  context('classic login', () => {
    useDatabaseResets();

    it('logs in successfully', () => {
      cy.create('App\\Models\\User', {name: 'Test Account'}).then(user => {
        cy.get('#email')
          .type(user.email)
        cy.get('#password')
          .type('password{enter}')
        cy.contains('Willkommä bim Qualix')
        cy.assertRedirect('/')
      })
    })

    it('validates wrong login', () => {
      cy.get('#email')
        .type('someone-who-doesnt-exist@qualix.flamberg.ch')

      cy.get('#password')
        .type('wrong{enter}')

      cy.contains('Dieses Login ist uns nicht bekannt. Meldest du dich vielleicht normalerweise mit MiData an?')
    })

  })

  context('MiData login', () => {
    useDatabaseResets();

    beforeEach(() => {
      cy.resetMocks()
    })

    it('logs in successfully', () => {
      cy.mockMiDataOAuth()

      cy.create('App\\Models\\HitobitoUser', {hitobito_id: '1234', email: 'oauther@email.com'}).then(user => {

        // Visit the URL that the MiData login button leads to, in order to get the the state token from the backend
        cy.request({
          url: '/login/hitobito',
          followRedirects: false
        })
          .then(response => {
            const state = response.headers.location.match(/[&?]state=([^&]+)/)[1]

            // OAuth application has redirected back to the callback route
            cy.visit('/login/hitobito/callback?code=foo&state=' + state)

            cy.contains('Willkommä bim Qualix')
            cy.assertRedirect('/')
          })
      })
    })

    it('fails gracefully when MiData is down', () => {

      // Visit the URL that the MiData login button leads to, in order to get the the state token from the backend
      cy.request({
        url: '/login/hitobito',
        followRedirects: false
      })
        .then(response => {
          const state = response.headers.location.match(/[&?]state=([^&]+)/)[1]

          // OAuth application has redirected back to the callback route
          cy.visit('/login/hitobito/callback?code=foo&state=' + state)

          cy.contains('Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere dich mit einem klassischen Account.')
        })

    })

    it('fails gracefully when permission on MiData not granted', () => {

        // Callback is called with error, this means the user has denied permission
        cy.visit('/login/hitobito/callback?code=foo&error=123')

        cy.contains('Zugriff in MiData verweigert.')

    })
  })
})
