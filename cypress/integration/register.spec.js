import { useDatabaseResets } from "../support/databaseTransactions"

describe('register page', () => {

  beforeEach(() => {
    cy.logout()
    cy.visit('/register')
  })

  it('displays the page title', () => {
    cy.get('.card-header').contains('Neu registrieren')
  })

  it('displays the login link', () => {
    cy.get('form').contains('Ich habe schon einen Account')
      .should('have.attr', 'href').and('match', /\/login$/)
  })

  it('requires name', () => {
    cy.get('#name')
      .should('have.attr', 'required')
  })

  it('requires email', () => {
    cy.get('#email')
      .should('have.attr', 'required')
  })

  it('requires password', () => {
    cy.get('#password')
      .should('have.attr', 'required')
  })

  it('requires password confirmation', () => {
    cy.get('#password-confirm')
      .should('have.attr', 'required')
  })

  it('displays the MiData registration button', () => {
    cy.contains('Via PBS MiData registrieren')
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

  context('classic registration', () => {
    useDatabaseResets();

    it('registers and verifies email successfully', () => {

      cy.generate('App\\Models\\User').then(user => {
        cy.get('#name').type(user.name)
        cy.get('#email').type(user.email)
        cy.get('#password').type('password')
        cy.get('#password-confirm').type('password{enter}')

        cy.url().should('match', /\/email\/verify$/)
        cy.get('.card-header').contains('E-Mail-Adresse verifizieren')
        cy.contains(`Du kannst den Link in deinen E-Mails unter ${user.email} zur Verifizierung verwenden.`)

        cy.lastSentMail()
          .then(mail => {
            const verifyLink = mail.match(/http:\/\/qualix\/email\/verify\/\S+/)[0]

            cy.visit(verifyLink)

            cy.contains('Willkommä bim Qualix')

            cy.contains('Du bist momentan noch in keinem Kurs eingetragen.')
          })
      })
    })

    it('validates existing email when registering', () => {
      cy.create('App\\Models\\User', {name: 'Test Account'}).then(user => {
        cy.get('#name').type('Bari')
        cy.get('#email').type(user.email)
        cy.get('#password').type('password')
        cy.get('#password-confirm').type('password{enter}')

        cy.contains('E-Mail-Adresse ist schon vergeben.')
      })
    })
  })

  context('MiData registration', () => {
    useDatabaseResets();

    beforeEach(() => {
      cy.resetMocks()
    })

    it('logs in successfully', () => {
      cy.mockMiDataOAuth()

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
