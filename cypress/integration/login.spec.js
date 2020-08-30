import { useDatabaseResets } from "../support/databaseTransactions"

describe('login page', () => {

  beforeEach(() => {
    cy.visit('/login')
  })

  it('displays the page title', () => {
    cy.contains('Anmelden')
  })

  it('displays the registration link', () => {
    cy.get('form').contains('Neu registrieren')
      .should('have.attr', 'href').and('match', /\/register$/)
  })

  it('displays the password forgotten link', () => {
    cy.contains('Passwort vergessen?')
      .should('have.attr', 'href').and('match', /\/password\/reset$/)
  })

  context('classic login', () => {

    it('requires email', () => {
      cy.get('#email')
        .should('have.attr', 'required')
    })

    it('requires password', () => {
      cy.get('#password')
        .should('have.attr', 'required')
    })

    context('with user in db', () => {
      useDatabaseResets();

      it('logs in successfully', () => {
        cy.create('App\\Models\\User', {name: 'Test Account'})
          .then(user => {
            cy.get('#email')
              .type(user.email)
            cy.get('#password')
              .type('password{enter}')
            cy.contains('Willkommä bim Qualix')
          })
      })

    })

    it('validates wrong login', () => {
      cy.get('#email')
        .type('test2@qualix.flamberg.ch')

      cy.get('#password')
        .type('wrong{enter}')

      cy.contains('Dieses Login ist uns nicht bekannt. Meldest du dich vielleicht normalerweise mit MiData an?')
    })

  })

  context('MiData login', () => {
    it('displays the MiData login button', () => {
      cy.contains('Via PBS MiData einloggen')
        .should('have.attr', 'href').and('match', /\/login\/hitobito$/)

      cy.request({
        url: '/login/hitobito',
        followRedirect: false // turn off following redirects
      })
        .then((resp) => {
          expect(resp.status).to.eq(302)
          expect(resp.redirectedToUrl).to.match(/^https:\/\/[a-zA-Z.-]+\/oauth\/authorize\?client_id=/)
        })
    })
  })
})
